[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$suiteRoot = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
$jmeterRoot = Join-Path $suiteRoot 'jmeter'
$expected = [ordered]@{
    'general-flow.jmx' = @('POST Login Klien','GET Klien Dashboard')
    'client-flow.jmx' = @('PF-02 - GET Form Pra-Pendaftaran','PF-03 - POST Formulir Perkara','PF-05 - GET Monitoring Status')
    'repair-flow.jmx' = @('PF-04 - POST Unggah Ulang Dokumen')
    'legal-flow.jmx' = @('PF-07 - POST Submit Verifikasi Berkas')
}

foreach ($file in $expected.Keys) {
    $path = Join-Path $jmeterRoot $file
    [xml] $xml = Get-Content -LiteralPath $path -Raw
    $raw = Get-Content -LiteralPath $path -Raw
    if ($raw -match '/klien/pra-pendaftaran/\$\{[^}]+\}/dokumen') { throw "$file contains the removed additional-upload endpoint." }
    if ($raw -match 'https://(?:www\.)?tnypartners\.com') { throw "$file contains a production URL." }
    if ($raw -notmatch 'RETEST_TARGET_HOST' -or $raw -notmatch 'RETEST_OWNER_AUTHORIZATION') { throw "$file lacks the Azure host or owner-authorization gate." }
    if ($raw -match 'RETEST_BASIC_USER|Directory Privacy|<AuthManager') { throw "$file contains Rumahweb-only Basic authentication." }
    if (@($xml.SelectNodes("//JSR223PreProcessor[@testname='Safety Gate - Azure Staging Only']")).Count -ne 1) { throw "$file lacks the Azure staging safety gate." }
    foreach ($label in $expected[$file]) {
        $count = @($xml.SelectNodes("//HTTPSamplerProxy[@testname='$label']")).Count
        if ($count -ne 1) { throw "$file must contain exactly one sampler named '$label'; found $count." }
    }
    Write-Host "OK XML and labels: $file"
}

$securityPath = Join-Path $suiteRoot 'security\run-security-tests.ps1'
$securityRaw = Get-Content -LiteralPath $securityPath -Raw
foreach ($number in 1..9) {
    $id = 'ST-{0:D2}' -f $number
    $count = [regex]::Matches($securityRaw, "Invoke-Probe '$id'").Count
    if ($count -ne 1) { throw "Security runner must contain exactly one $id probe; found $count." }
}
if ($securityRaw -match '/klien/pra-pendaftaran/\$[^/]+/dokumen') {
    throw 'Security runner contains the removed additional-upload endpoint.'
}
if ($securityRaw -notmatch '/klien/catatan-verifikasi/\$repairNoteId/perbaikan') {
    throw 'Security runner does not use the v1.0.0 document-repair endpoint.'
}
if ($securityRaw -notmatch "X-Powered-By") {
    throw 'ST-09 must preserve the X-Powered-By finding check.'
}
Write-Host 'OK mapping and v1.0.0 routes: ST-01 through ST-09'

$cloneEnv = Get-Content -LiteralPath (Join-Path $suiteRoot 'templates\azure-app-settings.template') -Raw
foreach ($requiredValue in @(
    'APP_URL=https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net',
    'AZURE_STORAGE_CONTAINER=documents',
    'AZURE_STORAGE_PREFIX=retest/v1.0.0/tnypartners',
    'AZURE_READINESS_PREFIX=release-gate/retest/v1.0.0/tnypartners',
    'TRUSTED_HOSTS=tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net',
    'TRUSTED_PROXIES=none'
)) {
    if (-not $cloneEnv.Contains($requiredValue)) { throw "Azure App Service settings template is missing: $requiredValue" }
}
if ($cloneEnv -match '(?m)^(APP_KEY|DB_PASSWORD|ADMIN_DEFAULT_PASSWORD|RESEND_API_KEY|AZURE_STORAGE_CONNECTION_STRING)=\S+') {
    throw 'Azure App Service settings template contains a secret-like value.'
}
Write-Host 'OK isolated Azure App Service settings template'

$scriptDirectories = @('scripts','security','zap') | ForEach-Object { Join-Path $suiteRoot $_ }
$scripts = Get-ChildItem -LiteralPath $scriptDirectories -Filter '*.ps1' -File
foreach ($script in $scripts) {
    $tokens = $null; $errors = $null
    [void][Management.Automation.Language.Parser]::ParseFile($script.FullName, [ref] $tokens, [ref] $errors)
    if ($errors.Count -gt 0) { throw "$($script.Name) has parser errors: $($errors.Message -join '; ')" }
    Write-Host "OK PowerShell syntax: $($script.Name)"
}
Write-Host 'Harness static validation passed.'
