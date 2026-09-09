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
if ($target.Scheme -ne 'https' -or $target.Host -ne 'retest.tnypartners.com') { throw 'ZAP target is locked to https://retest.tnypartners.com.' }
if ($target.Host -in @('tnypartners.com','www.tnypartners.com')) { throw 'PRODUCTION TARGET BLOCKED.' }
$authorization = [Environment]::GetEnvironmentVariable('RETEST_PROVIDER_AUTHORIZATION')
if ([string]::IsNullOrWhiteSpace($authorization)) { throw 'Written Rumahweb authorization reference is required.' }
$basicUser = [Environment]::GetEnvironmentVariable('RETEST_BASIC_USER')
$basicPassword = [Environment]::GetEnvironmentVariable('RETEST_BASIC_PASSWORD')
if ([string]::IsNullOrWhiteSpace($basicUser) -or [string]::IsNullOrWhiteSpace($basicPassword)) { throw 'Directory Privacy credentials are required.' }

function Quote-Yaml([string] $Value) { return "'$($Value.Replace("'", "''"))'" }

$zapDirectory = Join-Path $RunDirectory 'zap'
if (Test-Path -LiteralPath (Join-Path $zapDirectory 'zap-report.json')) { throw 'ZAP evidence already exists; do not overwrite it.' }
New-Item -ItemType Directory -Force -Path $zapDirectory | Out-Null
$tempPlan = Join-Path ([IO.Path]::GetTempPath()) "tny-zap-$([Guid]::NewGuid().ToString('N')).yaml"
$userYaml = Quote-Yaml $basicUser
$passwordYaml = Quote-Yaml $basicPassword
$reportDirYaml = Quote-Yaml $zapDirectory.Replace('\','/')
$plan = @"
env:
  contexts:
    - name: Rumahweb-Retest-Public
      urls:
        - 'https://retest.tnypartners.com'
      includePaths:
        - 'https://retest\.tnypartners\.com/(?:|login|register|forgot-password|up)(?:[/?#].*)?'
      excludePaths:
        - 'https://retest\.tnypartners\.com/(?:klien|admin|staf-legal|logout|email|password)(?:/.*)?'
      authentication:
        method: http
        parameters:
          hostname: 'retest.tnypartners.com'
          port: 443
          realm: ''
      sessionManagement:
        method: cookie
      users:
        - name: Directory-Privacy
          credentials:
            username: $userYaml
            password: $passwordYaml
  parameters:
    failOnError: true
    failOnWarning: false
    progressToStdout: true
jobs:
  - type: spider
    parameters:
      context: Rumahweb-Retest-Public
      user: Directory-Privacy
      url: 'https://retest.tnypartners.com/'
      maxDuration: 10
  - type: passiveScan-wait
    parameters:
      maxDuration: 10
  - type: activeScan
    parameters:
      context: Rumahweb-Retest-Public
      user: Directory-Privacy
      policy: Default Policy
      maxRuleDurationInMins: 5
      maxScanDurationInMins: 30
  - type: report
    parameters:
      template: traditional-json
      reportDir: $reportDirYaml
      reportFile: zap-report.json
      reportTitle: TNY Law Firm Rumahweb v1.0.0 Public Retest
  - type: report
    parameters:
      template: traditional-html
      reportDir: $reportDirYaml
      reportFile: zap-report.html
      reportTitle: TNY Law Firm Rumahweb v1.0.0 Public Retest
"@

$sanitized = $plan.Replace($userYaml, "'<redacted>'").Replace($passwordYaml, "'<redacted>'")
$sanitized | Set-Content -LiteralPath (Join-Path $zapDirectory 'automation-plan.sanitized.yaml') -Encoding utf8
[IO.File]::WriteAllText($tempPlan, $plan, [Text.UTF8Encoding]::new($false))
try {
    $output = & $ZapPath -cmd -autorun $tempPlan 2>&1
    $exitCode = $LASTEXITCODE
    $output | Set-Content -LiteralPath (Join-Path $zapDirectory 'zap-execution.log') -Encoding utf8
} finally {
    if (Test-Path -LiteralPath $tempPlan) { Remove-Item -LiteralPath $tempPlan -Force }
}

foreach ($file in Get-ChildItem -LiteralPath $zapDirectory -File) {
    $content = Get-Content -LiteralPath $file.FullName -Raw -ErrorAction SilentlyContinue
    if ($content -and ($content.Contains($basicUser) -or $content.Contains($basicPassword))) {
        Remove-Item -LiteralPath $file.FullName -Force
        throw "Secret detected and removed from ZAP evidence file: $($file.Name)"
    }
}
if ($exitCode -ne 0) { throw "ZAP Automation Framework failed with exit code $exitCode." }
if (-not (Test-Path -LiteralPath (Join-Path $zapDirectory 'zap-report.json'))) { throw 'ZAP JSON report was not produced.' }
[ordered]@{
    executed_at = (Get-Date).ToString('o'); target = $target.AbsoluteUri
    scope = 'public-only'; authenticated_application_scan = $false
    provider_authorization_reference = $authorization; zap_exit_code = $exitCode
} | ConvertTo-Json | Set-Content -LiteralPath (Join-Path $zapDirectory 'execution-metadata.json') -Encoding utf8
Write-Host "ZAP public-scope evidence created: $zapDirectory"

