[CmdletBinding()]
param(
    [string] $BaseUrl = $env:SECURITY_BASE_URL,
    [string] $OutputRoot = (Join-Path $PSScriptRoot '..\after\security-retest'),
    [string] $SessionCookieName = $env:SECURITY_SESSION_COOKIE_NAME
)

# Requires PowerShell 7+ for multipart upload testing. It deliberately does
# not contain credentials, test-resource IDs, or private document paths.
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if ($PSVersionTable.PSVersion.Major -lt 7) { throw 'PowerShell 7 or newer is required for the multipart ST-07 probe.' }
if ([string]::IsNullOrWhiteSpace($BaseUrl)) { throw 'Set SECURITY_BASE_URL before running the security retest.' }

$BaseUrl = $BaseUrl.TrimEnd('/')
$runId = Get-Date -Format 'yyyyMMdd-HHmmss'
$runDirectory = Join-Path $OutputRoot $runId
New-Item -ItemType Directory -Force -Path $runDirectory | Out-Null
$logFile = Join-Path $runDirectory 'security-test-execution.log'
$resultFile = Join-Path $runDirectory 'security-test-results.json'
$results = [ordered]@{}

function Write-Log([string] $Message) {
    $line = "$(Get-Date -Format o) $Message"
    Add-Content -LiteralPath $logFile -Value $line
    Write-Host $line
}

function Require-Environment([string] $Name) {
    $value = [Environment]::GetEnvironmentVariable($Name)
    if ([string]::IsNullOrWhiteSpace($value)) { throw "Missing required environment variable: $Name" }
    return $value
}

function Get-ResponseContent($Response) {
    if ($null -eq $Response) { return '' }
    try {
        $reader = [System.IO.StreamReader]::new($Response.GetResponseStream())
        return $reader.ReadToEnd()
    } catch { return '' }
}

function Invoke-Http {
    param(
        [Parameter(Mandatory)] [string] $Path,
        [ValidateSet('GET', 'POST')] [string] $Method = 'GET',
        [Microsoft.PowerShell.Commands.WebRequestSession] $Session,
        [hashtable] $Body,
        [hashtable] $Form
    )
    $arguments = @{ Uri = "$BaseUrl$Path"; Method = $Method; MaximumRedirection = 0; ErrorAction = 'Stop' }
    if ($null -ne $Session) { $arguments.WebSession = $Session }
    if ($null -ne $Body) { $arguments.Body = $Body }
    if ($null -ne $Form) { $arguments.Form = $Form }
    try {
        $response = Invoke-WebRequest @arguments
        return [pscustomobject]@{ StatusCode = [int] $response.StatusCode; Headers = $response.Headers; Content = [string] $response.Content; Session = $Session }
    } catch {
        $response = $_.Exception.Response
        if ($null -eq $response) { throw }
        return [pscustomobject]@{ StatusCode = [int] $response.StatusCode; Headers = $response.Headers; Content = Get-ResponseContent $response; Session = $Session }
    }
}

function Get-CsrfToken([Microsoft.PowerShell.Commands.WebRequestSession] $Session) {
    $response = Invoke-Http -Path '/login' -Session $Session
    if ($response.StatusCode -ne 200 -or $response.Content -notmatch 'name="_token"\s+value="([^"]+)"') { throw "Unable to obtain a CSRF token (HTTP $($response.StatusCode))." }
    return [pscustomobject]@{ Token = $Matches[1]; Response = $response }
}

function Get-CsrfSession {
    $session = [Microsoft.PowerShell.Commands.WebRequestSession]::new()
    $csrf = Get-CsrfToken $session
    return [pscustomobject]@{ Session = $session; Token = $csrf.Token; Response = $csrf.Response }
}

function Login-User([string] $Email, [string] $Password) {
    $initial = Get-CsrfSession
    $response = Invoke-Http -Path '/login' -Method POST -Session $initial.Session -Body @{ _token = $initial.Token; email = $Email; password = $Password }
    if ($response.StatusCode -notin 302, 303) { throw "Login did not redirect as expected (HTTP $($response.StatusCode))." }
    return [pscustomobject]@{ Session = $initial.Session; Response = $response }
}

function Set-Result([string] $Id, [ValidateSet('PASS', 'FAIL', 'ERROR')] [string] $Status, [string] $Evidence) {
    $results[$Id] = [ordered]@{ status = $Status; evidence = $Evidence }
    Write-Log "[$Status] $Id - $Evidence"
}

function Invoke-Probe([string] $Id, [scriptblock] $Probe) {
    try { & $Probe } catch { Set-Result $Id 'ERROR' $_.Exception.Message }
}

$clientEmail = Require-Environment 'SECURITY_CLIENT_EMAIL'
$clientPassword = Require-Environment 'SECURITY_CLIENT_PASSWORD'
$otherClientEmail = Require-Environment 'SECURITY_OTHER_CLIENT_EMAIL'
$otherClientPassword = Require-Environment 'SECURITY_OTHER_CLIENT_PASSWORD'
$legalEmail = Require-Environment 'SECURITY_LEGAL_EMAIL'
$legalPassword = Require-Environment 'SECURITY_LEGAL_PASSWORD'
$adminEmail = Require-Environment 'SECURITY_ADMIN_EMAIL'
$adminPassword = Require-Environment 'SECURITY_ADMIN_PASSWORD'
$otherCaseId = Require-Environment 'SECURITY_OTHER_CASE_ID'
$documentId = Require-Environment 'SECURITY_DOCUMENT_ID'
$privateDocumentPath = Require-Environment 'SECURITY_PRIVATE_DOCUMENT_PATH'
$uploadCaseId = Require-Environment 'SECURITY_UPLOAD_CASE_ID'
$maliciousFile = Require-Environment 'SECURITY_MALICIOUS_FILE'
if (-not (Test-Path -LiteralPath $maliciousFile)) { throw 'SECURITY_MALICIOUS_FILE does not point to an accessible local test file.' }

Write-Log "Starting ST-01 to ST-09 against $BaseUrl (run $runId)."

Invoke-Probe 'ST-01' {
    $initial = Get-CsrfSession
    $response = Invoke-Http -Path '/login' -Method POST -Session $initial.Session -Body @{ _token = $initial.Token; email = "' OR 1=1 --"; password = 'invalid-probe-password' }
    $location = [string] $response.Headers['Location']
    if (($response.StatusCode -notin 500, 501, 502, 503, 504) -and $location -notmatch '/dashboard') { Set-Result 'ST-01' 'PASS' "Injection payload rejected (HTTP $($response.StatusCode))."; return }
    Set-Result 'ST-01' 'FAIL' "Unexpected authentication or server response (HTTP $($response.StatusCode))."
}

Invoke-Probe 'ST-02' {
    $client = Login-User $clientEmail $clientPassword
    $payload = [uri]::EscapeDataString('<script>window.__xss_probe=1</script>')
    $response = Invoke-Http -Path "/klien/pra-pendaftaran?search=$payload" -Session $client.Session
    if ($response.StatusCode -eq 200 -and $response.Content -notmatch '<script>window\.__xss_probe=1</script>') { Set-Result 'ST-02' 'PASS' 'Search payload is not returned as an executable script.'; return }
    Set-Result 'ST-02' 'FAIL' "Unexpected response or raw script reflection (HTTP $($response.StatusCode))."
}

Invoke-Probe 'ST-03' {
    $session = [Microsoft.PowerShell.Commands.WebRequestSession]::new()
    $missing = Invoke-Http -Path '/login' -Method POST -Session $session -Body @{ email = 'csrf-probe@example.test'; password = 'invalid' }
    $invalid = Invoke-Http -Path '/login' -Method POST -Session $session -Body @{ _token = 'invalid'; email = 'csrf-probe@example.test'; password = 'invalid' }
    if ($missing.StatusCode -eq 419 -and $invalid.StatusCode -eq 419) { Set-Result 'ST-03' 'PASS' 'Missing and invalid CSRF tokens are rejected with HTTP 419.'; return }
    Set-Result 'ST-03' 'FAIL' "Expected 419; received $($missing.StatusCode) and $($invalid.StatusCode)."
}

Invoke-Probe 'ST-04' {
    $initial = Get-CsrfSession
    $setCookie = @($initial.Response.Headers.GetValues('Set-Cookie'))
    $sessionCookie = if ($SessionCookieName) { $setCookie | Where-Object { $_ -like "$SessionCookieName=*" } | Select-Object -First 1 } else { $setCookie | Where-Object { $_ -match 'session=' } | Select-Object -First 1 }
    $flagsValid = $sessionCookie -match '(?i);\s*secure' -and $sessionCookie -match '(?i);\s*httponly' -and $sessionCookie -match '(?i);\s*samesite=lax'
    $rateSession = [Microsoft.PowerShell.Commands.WebRequestSession]::new()
    $lastStatus = 0
    1..6 | ForEach-Object {
        $token = Get-CsrfToken $rateSession
        $lastStatus = (Invoke-Http -Path '/login' -Method POST -Session $rateSession -Body @{ _token = $token.Token; email = 'rate-limit-probe@example.test'; password = 'invalid' }).StatusCode
    }
    if ($flagsValid -and $lastStatus -eq 429) { Set-Result 'ST-04' 'PASS' 'Session cookie flags and login rate limiting are enforced.'; return }
    Set-Result 'ST-04' 'FAIL' "Cookie flags valid=$flagsValid; sixth invalid-login response HTTP $lastStatus."
}

Invoke-Probe 'ST-05' {
    $anonymous = Invoke-Http -Path '/admin/dashboard'
    $client = Login-User $clientEmail $clientPassword
    $legal = Login-User $legalEmail $legalPassword
    $admin = Login-User $adminEmail $adminPassword
    $checks = @(
        $anonymous.StatusCode -eq 302 -and ([string] $anonymous.Headers['Location']) -match '/login',
        (Invoke-Http -Path '/admin/dashboard' -Session $client.Session).StatusCode -eq 403,
        (Invoke-Http -Path '/staf-legal/verifikasi-berkas' -Session $client.Session).StatusCode -eq 403,
        (Invoke-Http -Path '/admin/dashboard' -Session $legal.Session).StatusCode -eq 403,
        (Invoke-Http -Path '/admin/dashboard' -Session $admin.Session).StatusCode -eq 200
    )
    if ($checks -notcontains $false) { Set-Result 'ST-05' 'PASS' 'Anonymous, client, legal-staff, and admin route controls matched expected responses.'; return }
    Set-Result 'ST-05' 'FAIL' 'One or more valid RBAC route checks failed.'
}

Invoke-Probe 'ST-06' {
    $client = Login-User $clientEmail $clientPassword
    $owner = Login-User $otherClientEmail $otherClientPassword
    $blocked = Invoke-Http -Path "/klien/pra-pendaftaran/$otherCaseId" -Session $client.Session
    $allowed = Invoke-Http -Path "/klien/pra-pendaftaran/$otherCaseId" -Session $owner.Session
    if ($blocked.StatusCode -eq 403 -and $allowed.StatusCode -eq 200) { Set-Result 'ST-06' 'PASS' 'Cross-client case access is forbidden and owner access succeeds.'; return }
    Set-Result 'ST-06' 'FAIL' "Expected 403/200; received $($blocked.StatusCode)/$($allowed.StatusCode)."
}

Invoke-Probe 'ST-07' {
    $client = Login-User $clientEmail $clientPassword
    $csrf = Get-CsrfToken $client.Session
    $response = Invoke-Http -Path "/klien/pra-pendaftaran/$uploadCaseId/dokumen" -Method POST -Session $client.Session -Form @{ _token = $csrf.Token; nama_dokumen = 'Upload validation probe'; jenis_dokumen = 'bukti'; file = Get-Item -LiteralPath $maliciousFile }
    if ($response.StatusCode -in 302, 422) { Set-Result 'ST-07' 'PASS' "Disallowed upload was rejected (HTTP $($response.StatusCode))."; return }
    Set-Result 'ST-07' 'FAIL' "Disallowed upload was not rejected as expected (HTTP $($response.StatusCode))."
}

Invoke-Probe 'ST-08' {
    $client = Login-User $clientEmail $clientPassword
    $owner = Login-User $otherClientEmail $otherClientPassword
    $anonymous = Invoke-Http -Path "/klien/dokumen/$documentId"
    $blocked = Invoke-Http -Path "/klien/dokumen/$documentId" -Session $client.Session
    $allowed = Invoke-Http -Path "/klien/dokumen/$documentId" -Session $owner.Session
    if ($anonymous.StatusCode -eq 302 -and $blocked.StatusCode -eq 403 -and $allowed.StatusCode -eq 200) { Set-Result 'ST-08' 'PASS' 'Document download requires authentication and ownership.'; return }
    Set-Result 'ST-08' 'FAIL' "Expected 302/403/200; received $($anonymous.StatusCode)/$($blocked.StatusCode)/$($allowed.StatusCode)."
}

Invoke-Probe 'ST-09' {
    $storage = Invoke-Http -Path ('/storage/' + $privateDocumentPath.TrimStart('/'))
    $landing = Invoke-Http -Path '/'
    $serverHeader = [string] $landing.Headers['Server']
    $headersValid = $landing.Headers['Content-Security-Policy'] -and
        $landing.Headers['X-Content-Type-Options'] -eq 'nosniff' -and
        $landing.Headers['Strict-Transport-Security'] -match 'max-age=' -and
        -not $landing.Headers['X-Powered-By'] -and
        $serverHeader -notmatch '(?i)(/|\d)'
    $localAssetsOnly = $landing.Content -notmatch 'fonts\.googleapis\.com|fonts\.gstatic\.com'
    if (($storage.StatusCode -in 403, 404) -and $headersValid -and $localAssetsOnly) {
        Set-Result 'ST-09' 'PASS' "Private storage is blocked (HTTP $($storage.StatusCode)); security headers and local font assets verified."
        return
    }
    Set-Result 'ST-09' 'FAIL' "Storage HTTP $($storage.StatusCode); header/server or external-font verification did not match the expected policy."
}

$results | ConvertTo-Json -Depth 4 | Set-Content -LiteralPath $resultFile -Encoding utf8
Write-Log "Security retest completed. Evidence: $runDirectory"
if (($results.Values | Where-Object { $_.status -ne 'PASS' }).Count -gt 0) { exit 1 }
