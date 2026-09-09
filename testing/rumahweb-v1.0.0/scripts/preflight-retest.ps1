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
if ($target.Scheme -ne 'https' -or $target.Host -ne 'retest.tnypartners.com') {
    throw 'Preflight is locked to https://retest.tnypartners.com.'
}
if ($target.Host -in @('tnypartners.com','www.tnypartners.com')) { throw 'PRODUCTION TARGET BLOCKED.' }

$authorization = Require-Environment 'RETEST_PROVIDER_AUTHORIZATION'
$testerLocation = Require-Environment 'RETEST_TESTER_LOCATION'
$basicUser = Require-Environment 'RETEST_BASIC_USER'
$basicPassword = Require-Environment 'RETEST_BASIC_PASSWORD'
if ([string]::IsNullOrWhiteSpace($JMeterPath) -or -not (Test-Path -LiteralPath $JMeterPath -PathType Leaf)) {
    throw "JMeter executable not found: $JMeterPath"
}
if ([string]::IsNullOrWhiteSpace($ZapPath) -or -not (Test-Path -LiteralPath $ZapPath -PathType Leaf)) {
    throw "ZAP executable not found: $ZapPath"
}

$releaseEvidence = Join-Path $RunDirectory 'release\release-artifact-verification.json'
if (-not (Test-Path -LiteralPath $releaseEvidence -PathType Leaf)) {
    throw 'Release artifact verification evidence is missing. Run verify-release-artifact.ps1 first.'
}
$release = Get-Content -LiteralPath $releaseEvidence -Raw | ConvertFrom-Json
$releaseCommit = [string] $release.manifest.commit
if ($releaseCommit -notmatch '^3b0ae8205b6e636df275f008162b25c6c8ec5bd1$') {
    throw "Release evidence is not v1.0.0 commit 3b0ae820...; found '$releaseCommit'."
}

$dns = @(Resolve-DnsName -Name $target.Host -Type A -ErrorAction Stop)
if ($dns.Count -eq 0) { throw 'Clone DNS A record was not resolved.' }

$basicToken = [Convert]::ToBase64String([Text.Encoding]::ASCII.GetBytes("$basicUser`:$basicPassword"))
$headers = @{ Authorization = "Basic $basicToken" }
$checks = [ordered]@{}
foreach ($probe in @(
    [pscustomobject]@{ Path='/'; Marker='Portal Pra-Pendaftaran Perkara' },
    [pscustomobject]@{ Path='/up'; Marker='' },
    [pscustomobject]@{ Path='/login'; Marker='Masuk' }
)) {
    $response = Invoke-WebRequest -Uri "$($target.AbsoluteUri.TrimEnd('/'))$($probe.Path)" -Headers $headers -TimeoutSec 20 -MaximumRedirection 0 -ErrorAction Stop
    $markerOk = [string]::IsNullOrEmpty($probe.Marker) -or $response.Content.Contains($probe.Marker)
    $checks[$probe.Path] = [ordered]@{ status_code=[int]$response.StatusCode; marker_ok=$markerOk }
    if ([int]$response.StatusCode -ne 200 -or -not $markerOk) { throw "Preflight failed for $($probe.Path)." }
}

$publicResponse = $null
try {
    $publicResponse = Invoke-WebRequest -Uri $target.AbsoluteUri -TimeoutSec 20 -MaximumRedirection 0 -ErrorAction Stop
} catch {
    if ($_.Exception.Response) { $publicResponse = $_.Exception.Response } else { throw }
}
$privacyStatus = [int] $publicResponse.StatusCode
if ($privacyStatus -ne 401) { throw "Directory Privacy is not enforced; unauthenticated request returned HTTP $privacyStatus." }

$output = Join-Path $RunDirectory 'preflight.json'
if (Test-Path -LiteralPath $output) { throw 'Preflight evidence already exists; use a new run directory.' }
[ordered]@{
    status = 'PASS'
    checked_at = (Get-Date).ToString('o')
    target_host = $target.Host
    production_target = $false
    provider_authorization_reference = $authorization
    tester_location = $testerLocation
    dns_addresses = @($dns | Where-Object IPAddress | Select-Object -ExpandProperty IPAddress -Unique)
    directory_privacy_unauthenticated_status = $privacyStatus
    endpoints = $checks
    release_commit = $releaseCommit
    jmeter_path_present = $true
    zap_path_present = $true
} | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath $output -Encoding utf8

Write-Host "Retest preflight passed: $output"
