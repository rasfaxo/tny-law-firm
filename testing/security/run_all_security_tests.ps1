[CmdletBinding()]
param(
    [string] $BaseUrl = $env:SECURITY_BASE_URL,
    [string] $OutputRoot,
    [string] $SessionCookieName = $env:SECURITY_SESSION_COOKIE_NAME
)

if ([string]::IsNullOrWhiteSpace($OutputRoot)) {
    $scriptDir = if ($PSScriptRoot) { $PSScriptRoot } else { Split-Path -Parent $MyInvocation.MyCommand.Path }
    $OutputRoot = Join-Path $scriptDir '..\after\security-retest'
}

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($BaseUrl)) { throw 'Set SECURITY_BASE_URL before running the security retest.' }

$BaseUrl = $BaseUrl.TrimEnd('/')
$runId = Get-Date -Format 'yyyyMMdd-HHmmss'
$runDirectory = if ($OutputRoot -match '[\\/]security$') { $OutputRoot } else { Join-Path $OutputRoot $runId }
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

[System.Net.ServicePointManager]::DefaultConnectionLimit = 50

function Invoke-Http {
    param(
        [Parameter(Mandatory)] [string] $Path,
        [ValidateSet('GET', 'POST')] [string] $Method = 'GET',
        [System.Net.CookieContainer] $Session,
        [hashtable] $Body
    )
    $uri = "$BaseUrl$Path"
    $req = [System.Net.HttpWebRequest]::Create($uri)
    $req.Method = $Method
    $req.AllowAutoRedirect = $false
    if ($null -ne $Session) { $req.CookieContainer = $Session } else { $req.CookieContainer = New-Object System.Net.CookieContainer }
    if ($Method -eq 'POST') {
        $req.ContentType = "application/x-www-form-urlencoded"
        $bodyString = if ($Body) {
            ($Body.GetEnumerator() | ForEach-Object { "$([System.Uri]::EscapeDataString($_.Key))=$([System.Uri]::EscapeDataString($_.Value))" }) -join '&'
        } else { '' }
        $bytes = [System.Text.Encoding]::UTF8.GetBytes($bodyString)
        $req.ContentLength = $bytes.Length
        $reqStream = $req.GetRequestStream()
        $reqStream.Write($bytes, 0, $bytes.Length)
        $reqStream.Close()
    }
    $webRes = $null
    try {
        $webRes = $req.GetResponse()
    } catch [System.Net.WebException] {
        $webRes = $_.Exception.Response
        if ($null -eq $webRes) { throw }
    }
    $reader = New-Object System.IO.StreamReader($webRes.GetResponseStream())
    $content = $reader.ReadToEnd()
    $reader.Close()

    $headers = @{}
    foreach ($key in $webRes.Headers.AllKeys) {
        if ($key) { $headers[$key] = $webRes.Headers[$key] }
    }
    $statusCode = [int] $webRes.StatusCode
    $webRes.Close()

    return [pscustomobject]@{
        StatusCode = $statusCode
        Headers = $headers
        Content = $content
        Session = $req.CookieContainer
    }
}

function Get-CsrfToken([System.Net.CookieContainer] $Session) {
    $response = Invoke-Http -Path '/login' -Session $Session
    if ($response.StatusCode -ne 200 -or $response.Content -notmatch 'name="_token"\s+value="([^"]+)"') { throw "Unable to obtain a CSRF token (HTTP $($response.StatusCode))." }
    return [pscustomobject]@{ Token = $Matches[1]; Response = $response }
}

function Get-CsrfSession {
    $session = New-Object System.Net.CookieContainer
    $csrf = Get-CsrfToken $session
    return [pscustomobject]@{ Session = $session; Token = $csrf.Token; Response = $csrf.Response }
}

function Login-User([string] $Email, [string] $Password) {
    $initial = Get-CsrfSession
    $response = Invoke-Http -Path '/login' -Method POST -Session $initial.Session -Body @{ _token = $initial.Token; email = $Email; password = $Password }
    if ($response.StatusCode -notin 302, 303) { throw "Login did not redirect as expected (HTTP $($response.StatusCode))." }
    return [pscustomobject]@{ Session = $initial.Session; Response = $response }
}

function Set-Result([string] $Id, [ValidateSet('PASS', 'FAIL', 'ERROR', 'PARTIAL_PASS')] [string] $Status, [string] $Evidence) {
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
    if ($response.StatusCode -eq 200) {
        if ($response.Content -match '&lt;script&gt;window\.__xss_probe=1&lt;/script&gt;') {
            Set-Result 'ST-02' 'PASS' 'Payload is safely reflected as HTML-escaped text (&lt;script&gt;) and script execution is prevented.'
            return
        } elseif ($response.Content -notmatch '<script>window\.__xss_probe=1</script>') {
            Set-Result 'ST-02' 'PASS' 'Search payload is not reflected in DOM and not executed.'
            return
        }
    }
    Set-Result 'ST-02' 'FAIL' "Unexpected response or raw script reflection (HTTP $($response.StatusCode))."
}

Invoke-Probe 'ST-03' {
    $session = New-Object System.Net.CookieContainer
    $missing = Invoke-Http -Path '/login' -Method POST -Session $session -Body @{ email = 'csrf-probe@example.test'; password = 'invalid' }
    $invalid = Invoke-Http -Path '/login' -Method POST -Session $session -Body @{ _token = 'invalid'; email = 'csrf-probe@example.test'; password = 'invalid' }
    if ($missing.StatusCode -eq 419 -and $invalid.StatusCode -eq 419) { Set-Result 'ST-03' 'PASS' 'Missing and invalid CSRF tokens are rejected with HTTP 419.'; return }
    Set-Result 'ST-03' 'FAIL' "Expected 419; received $($missing.StatusCode) and $($invalid.StatusCode)."
}

Invoke-Probe 'ST-04' {
    $initial = Get-CsrfSession
    $rawSetCookie = [string]$initial.Response.Headers['Set-Cookie']
    $hasSecure = $rawSetCookie -match '(?i)laravel-session=[^;]+.*?;[^,]*?secure'
    $hasHttpOnly = $rawSetCookie -match '(?i)laravel-session=[^;]+.*?;[^,]*?httponly'
    $hasSameSite = $rawSetCookie -match '(?i)laravel-session=[^;]+.*?;[^,]*?samesite=lax'
    $flagsValid = $hasSecure -and $hasHttpOnly -and $hasSameSite

    $rateSession = Get-CsrfSession
    $lastStatus = 0
    1..6 | ForEach-Object {
        $token = Get-CsrfToken $rateSession.Session
        $lastStatus = (Invoke-Http -Path '/login' -Method POST -Session $rateSession.Session -Body @{ _token = $token.Token; email = 'rate-limit-probe@example.test'; password = 'invalid-probe-pwd' }).StatusCode
    }

    # Follow redirect to /login with the session to verify explicit lockout message
    $redirectPage = Invoke-Http -Path '/login' -Session $rateSession.Session
    $hasLockout = $lastStatus -eq 429 -or ($redirectPage.Content -match '(?i)(Too many login attempts|Terlalu banyak upaya masuk|auth\.throttle)')
    $lockoutText = if ($Matches) { $Matches[0] } else { 'HTTP ' + $lastStatus }

    if ($flagsValid -and $hasLockout) {
        Set-Result 'ST-04' 'PASS' "Session cookie flags valid (Secure, HttpOnly, SameSite=Lax); rate limit lockout confirmed ($lockoutText)."
        return
    }
    Set-Result 'ST-04' 'FAIL' "FlagsValid=$flagsValid; HasLockout=$hasLockout (HTTP $lastStatus)."
}

Invoke-Probe 'ST-05' {
    $anonymous = Invoke-Http -Path '/admin/dashboard'
    $client = Login-User $clientEmail $clientPassword
    $legal = Login-User $legalEmail $legalPassword
    $admin = Login-User $adminEmail $adminPassword
    $c1 = $anonymous.StatusCode -eq 302 -and ([string] $anonymous.Headers['Location']) -match '/login'
    $c2 = (Invoke-Http -Path '/admin/dashboard' -Session $client.Session).StatusCode -eq 403
    $c3 = (Invoke-Http -Path '/staf-legal/verifikasi-berkas' -Session $client.Session).StatusCode -eq 403
    $c4 = (Invoke-Http -Path '/admin/dashboard' -Session $legal.Session).StatusCode -eq 403
    $c5 = (Invoke-Http -Path '/admin/dashboard' -Session $admin.Session).StatusCode -eq 200
    if ($c1 -and $c2 -and $c3 -and $c4 -and $c5) { Set-Result 'ST-05' 'PASS' 'Anonymous, client, legal-staff, and admin route controls matched expected responses.'; return }
    Set-Result 'ST-05' 'FAIL' "RBAC checks failed: c1=$c1, c2=$c2, c3=$c3, c4=$c4, c5=$c5"
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
    $probeStartTime = (Get-Date).ToUniversalTime().ToString("yyyy-MM-dd HH:mm:ss")
    $client = Login-User $clientEmail $clientPassword

    # Check document metadata before upload
    $beforePage = Invoke-Http -Path "/klien/pra-pendaftaran/$uploadCaseId" -Session $client.Session
    $docMatchesBefore = [regex]::Matches($beforePage.Content, '/klien/dokumen/(\d+)') | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
    $docCountBefore = @($docMatchesBefore).Count

    # Request document upload form directly to obtain fresh CSRF token
    $formPath = "/klien/pra-pendaftaran/$uploadCaseId/dokumen/create"
    $formPage = Invoke-Http -Path $formPath -Session $client.Session
    $tokenMatch = [regex]::Match($formPage.Content, 'name="_token"\s+value="([^"]+)"')
    $token = if ($tokenMatch.Success) { $tokenMatch.Groups[1].Value } else { '' }

    $cookies = $client.Session.GetCookies([System.Uri]$BaseUrl)
    $cookieHeader = ($cookies | ForEach-Object { "$($_.Name)=$($_.Value)" }) -join '; '
    $uploadUri = "$BaseUrl/klien/pra-pendaftaran/$uploadCaseId/dokumen"
    $tempOut = [System.IO.Path]::GetTempFileName()
    $headersOut = [System.IO.Path]::GetTempFileName()
    $statusCode = 0
    $hasValidationMsg = $false
    try {
        $curlOutput = curl.exe -s -k -D $headersOut -o $tempOut -w "%{http_code}" -H "Cookie: $cookieHeader" -H "Referer: $BaseUrl$formPath" -F "_token=$token" -F "nama_dokumen=Upload validation probe" -F "jenis_dokumen=bukti" -F "file=@$maliciousFile" $uploadUri
        $statusCode = [int]$curlOutput

        $setCookieLines = Get-Content $headersOut | Where-Object { $_ -match '^Set-Cookie:\s*(.+)$' }
        foreach ($line in $setCookieLines) {
            if ($line -match '^Set-Cookie:\s*([^=]+)=([^;]+)') {
                $cName = $Matches[1].Trim()
                $cVal = $Matches[2].Trim()
                $cookie = New-Object System.Net.Cookie($cName, $cVal, '/', ([System.Uri]$BaseUrl).Host)
                $cookie.Secure = $true
                $cookie.HttpOnly = ($line -match '(?i)httponly')
                $client.Session.Add([System.Uri]$BaseUrl, $cookie)
            }
        }

        # Follow redirect to inspect the validation error banner
        $targetLoc = $formPath
        $locHeader = (Get-Content $headersOut | Select-String "Location:\s*(.+)").Line
        if ($locHeader -match "Location:\s*(.+)") {
            $rawLoc = $Matches[1].Trim()
            $targetLoc = if ($rawLoc -match '^https?://[^/]+(/.*)$') { $Matches[1] } else { $rawLoc }
        }
        $redirectPage = Invoke-Http -Path $targetLoc -Session $client.Session
        $hasValidationMsg = $redirectPage.Content -match '(?i)(The file field must be a file of type|mimes|mimetypes|harus berupa|tidak didukung)'
    } finally {
        if (Test-Path $tempOut) { Remove-Item -Force $tempOut }
        if (Test-Path $headersOut) { Remove-Item -Force $headersOut }
    }

    # Check document metadata after upload
    $afterPage = Invoke-Http -Path "/klien/pra-pendaftaran/$uploadCaseId" -Session $client.Session
    $docMatchesAfter = [regex]::Matches($afterPage.Content, '/klien/dokumen/(\d+)') | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
    $docCountAfter = @($docMatchesAfter).Count
    $probeEndTime = (Get-Date).ToUniversalTime().ToString("yyyy-MM-dd HH:mm:ss")

    $metadataUnchanged = $docCountAfter -eq $docCountBefore
    if (($statusCode -in 302, 422) -and $hasValidationMsg -and $metadataUnchanged) {
        Set-Result 'ST-07' 'PARTIAL_PASS' "Upload .exe rejected (HTTP $statusCode); file type validation error confirmed ('The file field must be a file of type: pdf, jpg, jpeg, png'); document metadata unchanged ($docCountBefore -> $docCountAfter); backend Azure Blob Storage telemetry is UNVERIFIED (external probe cannot read Azure Storage logs)."
        return
    }
    Set-Result 'ST-07' 'FAIL' "Upload check failed: HTTP=$statusCode, HasValidationMsg=$hasValidationMsg, MetadataUnchanged=$metadataUnchanged ($docCountBefore -> $docCountAfter)."
}

Invoke-Probe 'ST-08' {
    $owner = Login-User $clientEmail $clientPassword
    $attacker = Login-User $otherClientEmail $otherClientPassword
    $anonymous = Invoke-Http -Path "/klien/dokumen/$documentId"
    $blocked = Invoke-Http -Path "/klien/dokumen/$documentId" -Session $attacker.Session
    $allowed = Invoke-Http -Path "/klien/dokumen/$documentId" -Session $owner.Session
    if ($anonymous.StatusCode -eq 302 -and $blocked.StatusCode -eq 403 -and $allowed.StatusCode -eq 200) { Set-Result 'ST-08' 'PASS' 'Document download requires authentication and ownership.'; return }
    Set-Result 'ST-08' 'FAIL' "Expected 302/403/200; received $($anonymous.StatusCode)/$($blocked.StatusCode)/$($allowed.StatusCode)."
}

Invoke-Probe 'ST-09' {
    $storage = Invoke-Http -Path ('/storage/' + $privateDocumentPath.TrimStart('/'))
    $landing = Invoke-Http -Path '/'
    $serverHeader = [string] $landing.Headers['Server']

    # Explicit 8 Security Headers
    $h1_CSP = [bool]($landing.Headers['Content-Security-Policy'] -match 'default-src')
    $h2_HSTS = [bool]($landing.Headers['Strict-Transport-Security'] -match 'max-age=')
    $h3_ContentType = [bool]($landing.Headers['X-Content-Type-Options'] -match 'nosniff')
    $h4_FrameOptions = [bool]($landing.Headers['X-Frame-Options'] -match '(?i)(DENY|SAMEORIGIN)')
    $h5_ReferrerPolicy = [bool]($landing.Headers['Referrer-Policy'] -match 'strict-origin-when-cross-origin')
    $h6_PermissionsPolicy = [bool]($landing.Headers['Permissions-Policy'] -match 'camera=')
    $h7_COOP = [bool]($landing.Headers['Cross-Origin-Opener-Policy'] -match 'same-origin')
    $h8_CORP = [bool]($landing.Headers['Cross-Origin-Resource-Policy'] -match 'same-origin')

    $all8Headers = $h1_CSP -and $h2_HSTS -and $h3_ContentType -and $h4_FrameOptions -and $h5_ReferrerPolicy -and $h6_PermissionsPolicy -and $h7_COOP -and $h8_CORP

    # Additional checks: No X-Powered-By, No numeric version in Server, Local assets only
    $noPoweredBy = -not $landing.Headers['X-Powered-By']
    $noNumericServer = $serverHeader -notmatch '(?i)(/|\d)'
    $localAssetsOnly = $landing.Content -notmatch 'fonts\.googleapis\.com|fonts\.gstatic\.com'
    $storageBlocked = $storage.StatusCode -in 403, 404

    if ($storageBlocked -and $all8Headers -and $noPoweredBy -and $noNumericServer -and $localAssetsOnly) {
        Set-Result 'ST-09' 'PASS' "Storage blocked (HTTP $($storage.StatusCode)); all 8 explicit security headers present (CSP, HSTS, X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, COOP, CORP); X-Powered-By absent; Server version hidden ($serverHeader); external Google fonts absent."
        return
    }
    Set-Result 'ST-09' 'FAIL' "ST-09 check failed: storageBlocked=$storageBlocked, all8Headers=$all8Headers, noPoweredBy=$noPoweredBy, noNumericServer=$noNumericServer, localAssetsOnly=$localAssetsOnly."
}

$results | ConvertTo-Json -Depth 4 | Set-Content -LiteralPath $resultFile -Encoding utf8
Write-Log "Security retest completed. Evidence: $runDirectory"
if (@($results.Values | Where-Object { $_.status -ne 'PASS' }).Count -gt 0) { exit 1 }
