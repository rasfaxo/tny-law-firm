[CmdletBinding()]
param(
    [Parameter(Mandatory)] [string] $RunDirectory,
    [string] $ZapPath = $env:ZAP_PATH,
    [string] $BaseUrl = $env:SECURITY_BASE_URL
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
if ([string]::IsNullOrWhiteSpace($ZapPath) -or -not (Test-Path -LiteralPath $ZapPath -PathType Leaf)) { throw 'Set ZAP_PATH to zap.bat.' }
if ([string]::IsNullOrWhiteSpace($BaseUrl)) { throw 'Set SECURITY_BASE_URL.' }
$target = [Uri] $BaseUrl.TrimEnd('/')
if ($target.Scheme -ne 'https' -or $target.Host -ne 'tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net') { throw 'ZAP target is locked to https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net.' }
if ($target.Host -in @('tnypartners.com','www.tnypartners.com')) { throw 'PRODUCTION TARGET BLOCKED.' }
$authorization = [Environment]::GetEnvironmentVariable('RETEST_OWNER_AUTHORIZATION')
if ([string]::IsNullOrWhiteSpace($authorization)) { throw 'Owner authorization reference is required.' }

function Quote-Yaml([string] $Value) { return "'$($Value.Replace("'", "''"))'" }

$zapDirectory = Join-Path $RunDirectory 'zap'
if (Test-Path -LiteralPath (Join-Path $zapDirectory 'zap-report.json')) { throw 'ZAP evidence already exists; do not overwrite it.' }
New-Item -ItemType Directory -Force -Path $zapDirectory | Out-Null
$tempPlan = Join-Path ([IO.Path]::GetTempPath()) "tny-zap-$([Guid]::NewGuid().ToString('N')).yaml"
$reportDirYaml = Quote-Yaml $zapDirectory.Replace('\','/')
$plan = @"
env:
  contexts:
    - name: Azure-V100-Retest-Public
      urls:
        - 'https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net'
      includePaths:
        - 'https://tny-law-firm-staging-afb3fqbdfvbteea3\.indonesiacentral-01\.azurewebsites\.net/(?:|login|register|forgot-password|up)(?:[/?#].*)?'
      excludePaths:
        - 'https://tny-law-firm-staging-afb3fqbdfvbteea3\.indonesiacentral-01\.azurewebsites\.net/(?:klien|admin|staf-legal|logout|email|password)(?:/.*)?'
  parameters:
    failOnError: true
    failOnWarning: false
    progressToStdout: true
jobs:
  - type: spider
    parameters:
      context: Azure-V100-Retest-Public
      url: 'https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net/'
      maxDuration: 10
  - type: passiveScan-wait
    parameters:
      maxDuration: 10
  - type: activeScan
    parameters:
      context: Azure-V100-Retest-Public
      policy: Default Policy
      maxRuleDurationInMins: 5
      maxScanDurationInMins: 30
  - type: report
    parameters:
      template: traditional-json
      reportDir: $reportDirYaml
      reportFile: zap-report.json
      reportTitle: TNY Law Firm Azure v1.0.0 Public Retest
  - type: report
    parameters:
      template: traditional-html
      reportDir: $reportDirYaml
      reportFile: zap-report.html
      reportTitle: TNY Law Firm Azure v1.0.0 Public Retest
"@

$sanitized = $plan
$sanitized | Set-Content -LiteralPath (Join-Path $zapDirectory 'automation-plan.sanitized.yaml') -Encoding utf8
[IO.File]::WriteAllText($tempPlan, $plan, [Text.UTF8Encoding]::new($false))
try {
    $output = & $ZapPath -cmd -autorun $tempPlan 2>&1
    $exitCode = $LASTEXITCODE
    $output | Set-Content -LiteralPath (Join-Path $zapDirectory 'zap-execution.log') -Encoding utf8
} finally {
    if (Test-Path -LiteralPath $tempPlan) { Remove-Item -LiteralPath $tempPlan -Force }
}

if ($exitCode -ne 0) { throw "ZAP Automation Framework failed with exit code $exitCode." }
if (-not (Test-Path -LiteralPath (Join-Path $zapDirectory 'zap-report.json'))) { throw 'ZAP JSON report was not produced.' }
[ordered]@{
    executed_at = (Get-Date).ToString('o'); target = $target.AbsoluteUri
    scope = 'public-only'; authenticated_application_scan = $false
    owner_authorization_reference = $authorization; zap_exit_code = $exitCode
} | ConvertTo-Json | Set-Content -LiteralPath (Join-Path $zapDirectory 'execution-metadata.json') -Encoding utf8
Write-Host "ZAP public-scope evidence created: $zapDirectory"
