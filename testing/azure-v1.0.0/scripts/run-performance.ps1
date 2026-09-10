[CmdletBinding()]
param(
    [string] $JMeterPath = $env:JMETER_PATH,
    [string] $OutputRoot,
    [string] $RunDirectory,
    [string] $RunId = (Get-Date -Format 'yyyyMMdd-HHmmss'),
    [ValidateRange(60, 300)] [int] $CooldownSeconds = 65
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$scriptDir = $PSScriptRoot
$suiteRoot = (Resolve-Path (Join-Path $scriptDir '..')).Path
$repoRoot = (Resolve-Path (Join-Path $suiteRoot '..\..')).Path
if ([string]::IsNullOrWhiteSpace($OutputRoot)) { $OutputRoot = Join-Path $repoRoot 'testing\retest' }

function Require-Environment([string] $Name) {
    $value = [Environment]::GetEnvironmentVariable($Name)
    if ([string]::IsNullOrWhiteSpace($value)) { throw "Missing required environment variable: $Name" }
    return $value
}

$targetHost = Require-Environment 'RETEST_TARGET_HOST'
if ($targetHost -in @('tnypartners.com', 'www.tnypartners.com')) { throw 'PRODUCTION TARGET BLOCKED.' }
if ($targetHost -ne 'tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net') { throw "Harness is locked to tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net, not '$targetHost'." }
$authorization = Require-Environment 'RETEST_OWNER_AUTHORIZATION'
$testerLocation = Require-Environment 'RETEST_TESTER_LOCATION'
$requiredSecrets = @('JMETER_CLIENT_EMAIL','JMETER_CLIENT_PASSWORD','JMETER_LEGAL_EMAIL','JMETER_LEGAL_PASSWORD','RETEST_CATEGORY_ID','RETEST_VALID_DOCUMENT')
foreach ($name in $requiredSecrets) { [void](Require-Environment $name) }

$validDocument = [Environment]::GetEnvironmentVariable('RETEST_VALID_DOCUMENT')
if (-not (Test-Path -LiteralPath $validDocument -PathType Leaf)) { throw 'RETEST_VALID_DOCUMENT is not an accessible file.' }
if ([string]::IsNullOrWhiteSpace($JMeterPath)) { throw 'Set JMETER_PATH to jmeter.bat or jmeter.' }
if (-not (Test-Path -LiteralPath $JMeterPath -PathType Leaf)) { throw "JMeter executable not found: $JMeterPath" }

$runDirectory = if ([string]::IsNullOrWhiteSpace($RunDirectory)) {
    Join-Path $OutputRoot "$RunId-azure-v100"
} else {
    [IO.Path]::GetFullPath($RunDirectory)
}
if (Test-Path -LiteralPath (Join-Path $runDirectory 'performance')) { throw "Performance evidence already exists; do not overwrite it: $runDirectory" }
$validationDir = Join-Path $runDirectory 'performance\validation'
$recordedDir = Join-Path $runDirectory 'performance\recorded'
New-Item -ItemType Directory -Force -Path $validationDir, $recordedDir | Out-Null

$flows = @(
    [pscustomobject]@{ Name='general'; File='general-flow.jmx'; Labels=@('POST Login Klien','GET Klien Dashboard') },
    [pscustomobject]@{ Name='client'; File='client-flow.jmx'; Labels=@('PF-02 - GET Form Pra-Pendaftaran','PF-03 - POST Formulir Perkara','PF-05 - GET Monitoring Status') },
    [pscustomobject]@{ Name='repair'; File='repair-flow.jmx'; Labels=@('PF-04 - POST Unggah Ulang Dokumen') },
    [pscustomobject]@{ Name='legal'; File='legal-flow.jmx'; Labels=@('PF-07 - POST Submit Verifikasi Berkas') }
)

function Invoke-JMeterRun {
    param([pscustomobject] $Flow, [int] $Threads, [int] $RampUp, [string] $Destination, [switch] $Validation)
    $name = if ($Validation) { "validation-$($Flow.Name)" } else { "${Threads}vu-$($Flow.Name)" }
    $jtl = Join-Path $Destination "$name.jtl"
    $log = Join-Path $Destination "$name-jmeter.log"
    $report = Join-Path $Destination "$name-report"
    $testPlan = Join-Path $suiteRoot "jmeter\$($Flow.File)"
    Write-Host "Running $name (threads=$Threads, ramp=$RampUp)..."
    & $JMeterPath -n -t $testPlan -l $jtl -j $log -JTHREADS=$Threads -JRAMPUP=$RampUp `
        -Jjmeter.save.saveservice.output_format=csv `
        -Jjmeter.save.saveservice.print_field_names=true `
        -Jjmeter.save.saveservice.timestamp_format=ms `
        -Jjmeter.save.saveservice.successful=true `
        -Jjmeter.save.saveservice.label=true `
        -Jjmeter.save.saveservice.response_code=true `
        -e -o $report
    if ($LASTEXITCODE -ne 0) { throw "JMeter failed for $name (exit $LASTEXITCODE). Evidence retained in $Destination." }
    $rows = @(Import-Csv -LiteralPath $jtl)
    foreach ($label in $Flow.Labels) {
        $samples = @($rows | Where-Object { $_.label -eq $label })
        $failedCount = @($samples | Where-Object { $_.success -ne 'true' }).Count
        if ($Validation -and $samples.Count -ne $Threads) { throw "$name expected $Threads '$label' samples, found $($samples.Count)." }
        if ($Validation -and $failedCount -gt 0) { throw "$name contains failed '$label' samples." }
        if (-not $Validation -and ($samples.Count -ne $Threads -or $failedCount -gt 0)) {
            Write-Warning "$name recorded '$label': samples=$($samples.Count)/$Threads, failed=$failedCount. Evidence is retained and later metrics must report it."
        }
    }
}

$gitSafeRoot = $repoRoot.Replace('\','/')
$commit = (& git -c "safe.directory=$gitSafeRoot" rev-parse HEAD 2>$null)
$tag = (& git -c "safe.directory=$gitSafeRoot" describe --tags --exact-match $commit 2>$null)
$jmeterVersionOutput = (& $JMeterPath --version 2>&1 | Out-String)
$jmeterVersionMatch = [regex]::Matches($jmeterVersionOutput, '(?m)\b(\d+\.\d+\.\d+)\s*$') | Select-Object -Last 1
$jmeterVersion = if ($null -ne $jmeterVersionMatch) { $jmeterVersionMatch.Groups[1].Value } else { 'NOT VERIFIED' }
if ($jmeterVersion -eq 'NOT VERIFIED') { throw 'Unable to determine the Apache JMeter version.' }
$manifest = [ordered]@{
    run_id = $RunId
    environment = 'azure-app-service-staging'
    target_host = $targetHost
    production_target = $false
    owner_authorization_reference = $authorization
    tester_location = $testerLocation
    started_at = (Get-Date).ToString('o')
    repository_commit = [string] $commit
    repository_exact_tag = [string] $tag
    expected_release_tag = 'v1.0.0'
    expected_release_commit = '3b0ae82'
    jmeter = [string] $jmeterVersion
    methodology = [ordered]@{
        loops = 1
        validation_run = '1 VU; excluded from recorded metrics'
        general = @('5 VU / 5 s','10 VU / 10 s','20 VU / 20 s')
        client_repair_legal = @('5 VU / 5 s','10 VU / 10 s','20 VU / 10 s')
        cooldown_between_runs_seconds = $CooldownSeconds
    }
}
$manifest | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath (Join-Path $runDirectory 'environment.json') -Encoding utf8

foreach ($flow in $flows) {
    Invoke-JMeterRun -Flow $flow -Threads 1 -RampUp 1 -Destination $validationDir -Validation
    Write-Host "Cooldown $CooldownSeconds seconds to isolate throttle windows..."
    Start-Sleep -Seconds $CooldownSeconds
}

foreach ($threads in @(5,10,20)) {
    foreach ($flow in $flows) {
        $ramp = if ($flow.Name -eq 'general') { $threads } elseif ($threads -eq 20) { 10 } else { $threads }
        Invoke-JMeterRun -Flow $flow -Threads $threads -RampUp $ramp -Destination $recordedDir
        if (-not ($threads -eq 20 -and $flow.Name -eq 'legal')) {
            Write-Host "Cooldown $CooldownSeconds seconds to isolate throttle windows..."
            Start-Sleep -Seconds $CooldownSeconds
        }
    }
}

$manifest.ended_at = (Get-Date).ToString('o')
$manifest | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath (Join-Path $runDirectory 'environment.json') -Encoding utf8
Write-Host "Recorded performance run completed: $runDirectory"
