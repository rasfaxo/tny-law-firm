[CmdletBinding()]
param(
    [Parameter(Mandatory)] [string] $RunDirectory,
    [string] $BaseUrl = $env:SECURITY_BASE_URL,
    [string] $JMeterPath = $env:JMETER_PATH,
    [string] $ZapPath = $env:ZAP_PATH
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

function Require-Environment([string] $Name) {
    $value = [Environment]::GetEnvironmentVariable($Name)
    if ([string]::IsNullOrWhiteSpace($value)) { throw "Missing required environment variable: $Name" }
    return $value
}

if ([string]::IsNullOrWhiteSpace($BaseUrl)) { throw 'Set SECURITY_BASE_URL.' }
$target = [Uri] $BaseUrl.TrimEnd('/')
if ($target.Scheme -ne 'https' -or $target.Host -ne 'tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net') {
    throw 'Preflight is locked to https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net.'
}
if ($target.Host -in @('tnypartners.com','www.tnypartners.com')) { throw 'PRODUCTION TARGET BLOCKED.' }

$authorization = Require-Environment 'RETEST_OWNER_AUTHORIZATION'
$testerLocation = Require-Environment 'RETEST_TESTER_LOCATION'
if ([string]::IsNullOrWhiteSpace($JMeterPath) -or -not (Test-Path -LiteralPath $JMeterPath -PathType Leaf)) {
    throw "JMeter executable not found: $JMeterPath"
}
if ([string]::IsNullOrWhiteSpace($ZapPath) -or -not (Test-Path -LiteralPath $ZapPath -PathType Leaf)) {
    throw "ZAP executable not found: $ZapPath"
}

$releaseEvidence = Join-Path $RunDirectory 'release\azure-deployment-verification.json'
if (-not (Test-Path -LiteralPath $releaseEvidence -PathType Leaf)) {
    throw 'Azure deployment verification evidence is missing. Run verify-azure-deployment.ps1 first.'
}
$release = Get-Content -LiteralPath $releaseEvidence -Raw | ConvertFrom-Json
$releaseCommit = [string] $release.deployed_commit
if ($releaseCommit -notmatch '^3b0ae8205b6e636df275f008162b25c6c8ec5bd1$') {
    throw "Release evidence is not v1.0.0 commit 3b0ae820...; found '$releaseCommit'."
}

$dns = @(Resolve-DnsName -Name $target.Host -Type A -ErrorAction Stop)
if ($dns.Count -eq 0) { throw 'Azure staging DNS A record was not resolved.' }

$checks = [ordered]@{}
foreach ($probe in @(
    [pscustomobject]@{ Path='/'; Marker='Portal Pra-Pendaftaran Perkara' },
    [pscustomobject]@{ Path='/up'; Marker='' },
    [pscustomobject]@{ Path='/login'; Marker='Masuk' }
)) {
    $response = Invoke-WebRequest -Uri "$($target.AbsoluteUri.TrimEnd('/'))$($probe.Path)" -TimeoutSec 20 -MaximumRedirection 0 -ErrorAction Stop
    $markerOk = [string]::IsNullOrEmpty($probe.Marker) -or $response.Content.Contains($probe.Marker)
    $checks[$probe.Path] = [ordered]@{ status_code=[int]$response.StatusCode; marker_ok=$markerOk }
    if ([int]$response.StatusCode -ne 200 -or -not $markerOk) { throw "Preflight failed for $($probe.Path)." }
}

$output = Join-Path $RunDirectory 'preflight.json'
if (Test-Path -LiteralPath $output) { throw 'Preflight evidence already exists; use a new run directory.' }
[ordered]@{
    status = 'PASS'
    checked_at = (Get-Date).ToString('o')
    target_host = $target.Host
    production_target = $false
    owner_authorization_reference = $authorization
    tester_location = $testerLocation
    dns_addresses = @(
        $dns |
            Where-Object {
                $_.PSObject.Properties.Name -contains 'IPAddress' -and
                -not [string]::IsNullOrWhiteSpace([string] $_.IPAddress)
            } |
            ForEach-Object { [string] $_.IPAddress } |
            Sort-Object -Unique
    )
    endpoints = $checks
    release_commit = $releaseCommit
    jmeter_path_present = $true
    zap_path_present = $true
} | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath $output -Encoding utf8

Write-Host "Retest preflight passed: $output"
