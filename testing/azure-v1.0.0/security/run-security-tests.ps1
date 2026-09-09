[CmdletBinding()]
param(
    [string] $BaseUrl = $env:SECURITY_BASE_URL,
    [string] $OutputRoot,
    [string] $SessionCookieName = $env:SECURITY_SESSION_COOKIE_NAME
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

if ([string]::IsNullOrWhiteSpace($BaseUrl)) { throw 'Set SECURITY_BASE_URL.' }
$BaseUrl = $BaseUrl.TrimEnd('/')
$target = [Uri] $BaseUrl
if ($target.Scheme -ne 'https') { throw 'Security retest requires HTTPS.' }
if ($target.Host -in @('tnypartners.com', 'www.tnypartners.com')) {
    throw 'PRODUCTION TARGET BLOCKED. Use the isolated retest host.'
}
if ($target.Host -ne 'tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net') {
    throw "Unexpected target '$($target.Host)'. This harness is locked to tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net."
}
if ([string]::IsNullOrWhiteSpace($env:RETEST_OWNER_AUTHORIZATION)) {
    throw 'Set RETEST_OWNER_AUTHORIZATION to the owner authorization reference.'
}
if ([string]::IsNullOrWhiteSpace($SessionCookieName)) { $SessionCookieName = 'tny-law-firm-session' }

$scriptDir = $PSScriptRoot
if ([string]::IsNullOrWhiteSpace($OutputRoot)) { $OutputRoot = Join-Path $scriptDir '..\..\retest' }
$runId = Get-Date -Format 'yyyyMMdd-HHmmss'
$outputLeaf = Split-Path -Leaf $OutputRoot.TrimEnd('\','/')
$runRoot = if ($outputLeaf -match '^\d{8}-\d{6}-azure-v100$') { $OutputRoot } else { Join-Path $OutputRoot "$runId-azure-v100" }
$runDirectory = Join-Path $runRoot 'security'
New-Item -ItemType Directory -Force -Path $runDirectory | Out-Null
$logFile = Join-Path $runDirectory 'security-test-execution.log'
$resultFile = Join-Path $runDirectory 'security-test-results.json'
$headerFile = Join-Path $runDirectory 'st09-response-headers.json'
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

function Invoke-Http {
    param(
        [Parameter(Mandatory)] [string] $Path,
        [ValidateSet('GET', 'POST')] [string] $Method = 'GET',
        [System.Net.CookieContainer] $Session,
        [hashtable] $Body
    )
    $uri = if ($Path -match '^https://') { $Path } else { "$BaseUrl$Path" }
    $request = [System.Net.HttpWebRequest]::Create($uri)
    $request.Method = $Method
    $request.AllowAutoRedirect = $false
    if ($null -eq $Session) { $Session = [System.Net.CookieContainer]::new() }
    $request.CookieContainer = $Session
    if ($Method -eq 'POST') {
        $request.ContentType = 'application/x-www-form-urlencoded'
        $bodyString = if ($Body) {
            ($Body.GetEnumerator() | ForEach-Object {
                "$([Uri]::EscapeDataString([string] $_.Key))=$([Uri]::EscapeDataString([string] $_.Value))"
            }) -join '&'
        } else { '' }
        $bytes = [Text.Encoding]::UTF8.GetBytes($bodyString)
        $request.ContentLength = $bytes.Length
        $stream = $request.GetRequestStream()
        try { $stream.Write($bytes, 0, $bytes.Length) } finally { $stream.Dispose() }
    }
    $response = $null
    try { $response = $request.GetResponse() } catch [System.Net.WebException] {
        $response = $_.Exception.Response
        if ($null -eq $response) { throw }
    }
    $reader = [IO.StreamReader]::new($response.GetResponseStream())
    try { $content = $reader.ReadToEnd() } finally { $reader.Dispose() }
    $headers = [ordered]@{}
    foreach ($key in $response.Headers.AllKeys) { if ($key) { $headers[$key] = $response.Headers[$key] } }
    $status = [int] $response.StatusCode
    $response.Dispose()
    [pscustomobject]@{ StatusCode = $status; Headers = $headers; Content = $content; Session = $Session }
}

function Invoke-MultipartHttp {
    param(
        [Parameter(Mandatory)] [string] $Path,
        [Parameter(Mandatory)] [System.Net.CookieContainer] $Session,
        [Parameter(Mandatory)] [hashtable] $Fields,
        [Parameter(Mandatory)] [string] $FilePath,
        [Parameter(Mandatory)] [string] $FileField,
        [string] $MimeType = 'application/octet-stream'
    )
    Add-Type -AssemblyName System.Net.Http
    $handler = [Net.Http.HttpClientHandler]::new()
    $handler.AllowAutoRedirect = $false
    $handler.CookieContainer = $Session
    $client = [Net.Http.HttpClient]::new($handler)
    $multipart = [Net.Http.MultipartFormDataContent]::new()
    $fileStream = $null
    try {
        foreach ($entry in $Fields.GetEnumerator()) {
            $multipart.Add([Net.Http.StringContent]::new([string] $entry.Value), [string] $entry.Key)
        }
        $fileStream = [IO.File]::OpenRead($FilePath)
        $fileContent = [Net.Http.StreamContent]::new($fileStream)
        $fileContent.Headers.ContentType = [Net.Http.Headers.MediaTypeHeaderValue]::new($MimeType)
        $multipart.Add($fileContent, $FileField, [IO.Path]::GetFileName($FilePath))
        $response = $client.PostAsync("$BaseUrl$Path", $multipart).GetAwaiter().GetResult()
        $content = $response.Content.ReadAsStringAsync().GetAwaiter().GetResult()
        $headers = [ordered]@{}
        foreach ($header in $response.Headers) { $headers[$header.Key] = ($header.Value -join ', ') }
        [pscustomobject]@{ StatusCode = [int] $response.StatusCode; Headers = $headers; Content = $content; Session = $Session }
    } finally {
        if ($null -ne $fileStream) { $fileStream.Dispose() }
        $multipart.Dispose(); $client.Dispose(); $handler.Dispose()
    }
}

function Get-CsrfToken([System.Net.CookieContainer] $Session, [string] $Path = '/login') {
    $response = Invoke-Http -Path $Path -Session $Session
    $match = [regex]::Match($response.Content, 'name="_token"\s+value="([^"]+)"')
    if ($response.StatusCode -ne 200 -or -not $match.Success) {
        throw "Unable to obtain a CSRF token from $Path (HTTP $($response.StatusCode))."
    }
    [pscustomobject]@{ Token = $match.Groups[1].Value; Response = $response }
}

function Login-User([string] $Email, [string] $Password) {
    $session = [System.Net.CookieContainer]::new()
    $initial = Get-CsrfToken $session
    $response = Invoke-Http -Path '/login' -Method POST -Session $session -Body @{
        _token = $initial.Token; email = $Email; password = $Password
    }
    if ($response.StatusCode -notin 302, 303) { throw "Login did not redirect (HTTP $($response.StatusCode))." }
    [pscustomobject]@{ Session = $session; Response = $response }
}

function Set-Result {
    param([string] $Id, [ValidateSet('PASS', 'FAIL', 'ERROR', 'PARTIAL_PASS')] [string] $Status, [string] $Evidence)
    $results[$Id] = [ordered]@{ status = $Status; evidence = $Evidence }
    Write-Log "[$Status] $Id - $Evidence"
}

function Invoke-Probe([string] $Id, [scriptblock] $Probe) {
    try { & $Probe } catch { Set-Result $Id 'ERROR' $_.Exception.Message }
}

function Get-CaseLinkCounts([string] $Marker, [System.Net.CookieContainer] $Session) {
    $response = Invoke-Http -Path "/klien/pra-pendaftaran?search=$([Uri]::EscapeDataString($Marker))" -Session $Session
    $caseIds = [regex]::Matches($response.Content, '/klien/pra-pendaftaran/([0-9]+)') | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
    $documentIds = [regex]::Matches($response.Content, '/klien/dokumen/([0-9]+)') | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique
    [pscustomobject]@{ StatusCode = $response.StatusCode; CaseCount = @($caseIds).Count; DocumentCount = @($documentIds).Count }
}

function Get-AzureBlobCount([string] $ContainerUrl, [string] $Sas, [string] $Prefix) {
    $query = $Sas.TrimStart('?')
    $separator = if ($ContainerUrl.Contains('?')) { '&' } else { '?' }
    $uri = "$($ContainerUrl.TrimEnd('/'))${separator}restype=container&comp=list&prefix=$([Uri]::EscapeDataString($Prefix))&$query"
    $request = [System.Net.HttpWebRequest]::Create($uri)
    $request.Method = 'GET'; $request.AllowAutoRedirect = $false
    $response = $null
    try { $response = $request.GetResponse() } catch [System.Net.WebException] {
        $response = $_.Exception.Response
        if ($null -eq $response) { throw }
    }
    $status = [int] $response.StatusCode
    $reader = [IO.StreamReader]::new($response.GetResponseStream())
    try { $body = $reader.ReadToEnd() } finally { $reader.Dispose(); $response.Dispose() }
    if ($status -ne 200) { throw "Azure list failed with HTTP $status." }
    return [regex]::Matches($body, '<Name>.*?</Name>').Count
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
$privateDocumentPath = Require-Environment 'SECURITY_PRIVATE_DOCUMENT_PATH'
$categoryId = Require-Environment 'SECURITY_CATEGORY_ID'
$maliciousFile = Require-Environment 'SECURITY_MALICIOUS_FILE'
$repairNoteId = Require-Environment 'SECURITY_REPAIR_NOTE_ID'
$repairCaseId = Require-Environment 'SECURITY_REPAIR_CASE_ID'
$repairOldDocumentId = Require-Environment 'SECURITY_REPAIR_OLD_DOCUMENT_ID'
$repairReplacementFile = Require-Environment 'SECURITY_REPAIR_REPLACEMENT_FILE'
$azureContainerUrl = Require-Environment 'SECURITY_AZURE_CONTAINER_URL'
$azureSas = Require-Environment 'SECURITY_AZURE_SAS'
$azurePrefix = Require-Environment 'SECURITY_AZURE_PREFIX'
if (-not (Test-Path -LiteralPath $maliciousFile -PathType Leaf)) { throw 'SECURITY_MALICIOUS_FILE is not an accessible file.' }
if (-not (Test-Path -LiteralPath $repairReplacementFile -PathType Leaf)) { throw 'SECURITY_REPAIR_REPLACEMENT_FILE is not an accessible file.' }

Write-Log "Starting ST-01 to ST-09 against the isolated host $($target.Host) (run $runId)."

Invoke-Probe 'ST-01' {
    $client = Login-User $clientEmail $clientPassword; $legal = Login-User $legalEmail $legalPassword; $admin = Login-User $adminEmail $adminPassword
    $valid = (Invoke-Http -Path '/klien/dashboard' -Session $client.Session).StatusCode -eq 200 -and
        (Invoke-Http -Path '/staf-legal/dashboard' -Session $legal.Session).StatusCode -eq 200 -and
        (Invoke-Http -Path '/admin/dashboard' -Session $admin.Session).StatusCode -eq 200
    $anonymous = Invoke-Http -Path '/klien/dashboard'
    $session = [System.Net.CookieContainer]::new(); $csrf = Get-CsrfToken $session
    $injection = Invoke-Http -Path '/login' -Method POST -Session $session -Body @{ _token = $csrf.Token; email = "' OR 1=1 --"; password = 'invalid-probe-password' }
    $injectionLocation = [string] $injection.Headers['Location']
    $safeInvalid = $injection.StatusCode -notin 500,501,502,503,504 -and $injectionLocation -notmatch '/dashboard'
    $anonymousBlocked = $anonymous.StatusCode -eq 302 -and ([string] $anonymous.Headers['Location']) -match '/login'
    if ($valid -and $safeInvalid -and $anonymousBlocked) { Set-Result ST-01 PASS 'Valid role logins succeeded; SQLi credential was rejected; anonymous protected access redirected to login.' }
    else { Set-Result ST-01 FAIL "validLogins=$valid; injectionRejected=$safeInvalid; anonymousBlocked=$anonymousBlocked." }
}

Invoke-Probe 'ST-02' {
    $client = Login-User $clientEmail $clientPassword
    $allowed = (Invoke-Http -Path '/klien/dashboard' -Session $client.Session).StatusCode -eq 200
    $adminBlocked = (Invoke-Http -Path '/admin/dashboard' -Session $client.Session).StatusCode -eq 403
    $legalBlocked = (Invoke-Http -Path '/staf-legal/verifikasi-berkas' -Session $client.Session).StatusCode -eq 403
    if ($allowed -and $adminBlocked -and $legalBlocked) { Set-Result ST-02 PASS 'Client resource succeeded; direct Admin and Staf Legal URLs returned HTTP 403.' }
    else { Set-Result ST-02 FAIL "clientAllowed=$allowed; adminBlocked=$adminBlocked; legalBlocked=$legalBlocked." }
}

Invoke-Probe 'ST-03' {
    $admin = Login-User $adminEmail $adminPassword
    $allowed = (Invoke-Http -Path '/admin/dashboard' -Session $admin.Session).StatusCode -eq 200
    $legalBlocked = (Invoke-Http -Path '/staf-legal/verifikasi-berkas' -Session $admin.Session).StatusCode -eq 403
    $anonymous = Invoke-Http -Path '/admin/dashboard'
    $anonymousBlocked = $anonymous.StatusCode -eq 302 -and ([string] $anonymous.Headers['Location']) -match '/login'
    if ($allowed -and $legalBlocked -and $anonymousBlocked) { Set-Result ST-03 PASS 'Admin resource succeeded; Staf Legal verification and anonymous Admin access were blocked.' }
    else { Set-Result ST-03 FAIL "adminAllowed=$allowed; legalBlocked=$legalBlocked; anonymousBlocked=$anonymousBlocked." }
}

Invoke-Probe 'ST-04' {
    $legal = Login-User $legalEmail $legalPassword
    $dashboard = (Invoke-Http -Path '/staf-legal/dashboard' -Session $legal.Session).StatusCode -eq 200
    $queue = (Invoke-Http -Path '/staf-legal/verifikasi-berkas' -Session $legal.Session).StatusCode -eq 200
    $history = (Invoke-Http -Path '/staf-legal/riwayat-verifikasi' -Session $legal.Session).StatusCode -eq 200
    $adminBlocked = (Invoke-Http -Path '/admin/dashboard' -Session $legal.Session).StatusCode -eq 403
    $anonymous = Invoke-Http -Path '/staf-legal/verifikasi-berkas'
    $anonymousBlocked = $anonymous.StatusCode -eq 302 -and ([string] $anonymous.Headers['Location']) -match '/login'
    if ($dashboard -and $queue -and $history -and $adminBlocked -and $anonymousBlocked) { Set-Result ST-04 PASS 'Staf Legal resources succeeded; Admin and anonymous direct access were blocked.' }
    else { Set-Result ST-04 FAIL "dashboard=$dashboard; queue=$queue; history=$history; adminBlocked=$adminBlocked; anonymousBlocked=$anonymousBlocked." }
}

Write-Log 'Waiting 65 seconds so the login route throttle window cannot contaminate ST-05 through ST-08.'
Start-Sleep -Seconds 65

Invoke-Probe 'ST-05' {
    $session = [System.Net.CookieContainer]::new(); $initial = Get-CsrfToken $session
    $setCookie = [string] $initial.Response.Headers['Set-Cookie']; $cookiePattern = [regex]::Escape($SessionCookieName)
    $flags = $setCookie -match "(?i)$cookiePattern=[^;]+.*?secure" -and $setCookie -match "(?i)$cookiePattern=[^;]+.*?httponly" -and $setCookie -match "(?i)$cookiePattern=[^;]+.*?samesite=lax"
    $missing = Invoke-Http -Path '/login' -Method POST -Session ([System.Net.CookieContainer]::new()) -Body @{ email='csrf@example.test'; password='invalid' }
    $invalid = Invoke-Http -Path '/login' -Method POST -Session ([System.Net.CookieContainer]::new()) -Body @{ _token='invalid'; email='csrf@example.test'; password='invalid' }
    $client = Login-User $clientEmail $clientPassword
    $logoutCsrf = Get-CsrfToken $client.Session '/klien/dashboard'
    $logout = Invoke-Http -Path '/logout' -Method POST -Session $client.Session -Body @{ _token=$logoutCsrf.Token }
    $afterLogout = Invoke-Http -Path '/klien/dashboard' -Session $client.Session
    $last = 0; $rateSession = [System.Net.CookieContainer]::new()
    1..6 | ForEach-Object { $csrf = Get-CsrfToken $rateSession; $last = (Invoke-Http -Path '/login' -Method POST -Session $rateSession -Body @{ _token=$csrf.Token; email='rate-limit-probe@example.test'; password='invalid' }).StatusCode }
    $limited = $last -eq 429 -or (Invoke-Http -Path '/login' -Session $rateSession).Content -match '(?i)(Too many login attempts|Terlalu banyak upaya masuk)'
    $csrfProtected = $missing.StatusCode -eq 419 -and $invalid.StatusCode -eq 419
    $logoutProtected = $logout.StatusCode -in 302,303 -and $afterLogout.StatusCode -eq 302
    if ($flags -and $csrfProtected -and $logoutProtected -and $limited) { Set-Result ST-05 PASS 'Secure session cookie, CSRF rejection, logout invalidation, and login throttle were confirmed.' }
    else { Set-Result ST-05 FAIL "cookieFlags=$flags; csrf=$csrfProtected; logout=$logoutProtected; throttle=$limited." }
}

Write-Log 'Waiting 65 seconds so ST-05 throttle probes cannot contaminate ST-06 through ST-08.'
Start-Sleep -Seconds 65

Invoke-Probe 'ST-06' {
    $client = Login-User $clientEmail $clientPassword; $owner = Login-User $otherClientEmail $otherClientPassword
    $payload = [Uri]::EscapeDataString('<script>window.__xss_probe=1</script>')
    $xss = Invoke-Http -Path "/klien/pra-pendaftaran?search=$payload" -Session $client.Session
    $blocked = Invoke-Http -Path "/klien/pra-pendaftaran/$otherCaseId" -Session $client.Session
    $allowed = Invoke-Http -Path "/klien/pra-pendaftaran/$otherCaseId" -Session $owner.Session
    $escaped = $xss.StatusCode -eq 200 -and $xss.Content -notmatch '<script>window\.__xss_probe=1</script>'
    if ($escaped -and $blocked.StatusCode -eq 403 -and $allowed.StatusCode -eq 200) { Set-Result ST-06 PASS 'Raw XSS was not reflected; cross-client IDOR was blocked; owner access succeeded.' }
    else { Set-Result ST-06 FAIL "xssEscaped=$escaped; expected403/200=$($blocked.StatusCode)/$($allowed.StatusCode)." }
}

Invoke-Probe 'ST-07' {
    $marker = "ST07-$runId-$([Guid]::NewGuid().ToString('N').Substring(0,8))"
    $client = Login-User $clientEmail $clientPassword
    $beforeDb = Get-CaseLinkCounts $marker $client.Session
    $beforeBlob = Get-AzureBlobCount $azureContainerUrl $azureSas $azurePrefix
    $form = Get-CsrfToken $client.Session '/klien/pra-pendaftaran/create'
    $upload = Invoke-MultipartHttp -Path '/klien/pra-pendaftaran' -Session $client.Session -Fields @{
        _token = $form.Token; id_kategori = $categoryId; judul_perkara = $marker
        kronologi = 'Anonymous ST-07 invalid file-type validation fixture.'
        'dokumen[0][nama_dokumen]' = 'Executable rejection probe'; 'dokumen[0][jenis_dokumen]' = 'identitas'
    } -FilePath $maliciousFile -FileField 'dokumen[0][file_dokumen]'
    $location = [string] $upload.Headers['Location']
    $path = if ($location -match '^https?://[^/]+(/.*)$') { $Matches[1] } elseif ($location.StartsWith('/')) { $location } else { '/klien/pra-pendaftaran/create' }
    $validation = Invoke-Http -Path $path -Session $client.Session
    $afterDb = Get-CaseLinkCounts $marker $client.Session
    $afterBlob = Get-AzureBlobCount $azureContainerUrl $azureSas $azurePrefix
    $rejected = $upload.StatusCode -in 302,422
    $message = $validation.Content -match '(?i)(pdf|jpg|jpeg|png|format|tipe|mimes|mimetypes)'
    $dbUnchanged = $beforeDb.CaseCount -eq $afterDb.CaseCount -and $beforeDb.DocumentCount -eq $afterDb.DocumentCount
    $blobUnchanged = $beforeBlob -eq $afterBlob
    [ordered]@{
        marker = $marker; upload_http_status = $upload.StatusCode
        rendered_before = [ordered]@{ cases=$beforeDb.CaseCount; documents=$beforeDb.DocumentCount }
        rendered_after = [ordered]@{ cases=$afterDb.CaseCount; documents=$afterDb.DocumentCount }
        azure_prefix_blob_count_before = $beforeBlob; azure_prefix_blob_count_after = $afterBlob
        validation_message_detected = $message
    } | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath (Join-Path $runDirectory 'st07-probe-evidence.json') -Encoding utf8
    if ($rejected -and $message -and $dbUnchanged -and $blobUnchanged) {
        Set-Result ST-07 PASS "Executable rejected (HTTP $($upload.StatusCode)); rendered case/document counts unchanged ($($beforeDb.CaseCount)/$($beforeDb.DocumentCount)); Azure prefix blob count unchanged ($beforeBlob)."
    } else { Set-Result ST-07 FAIL "rejected=$rejected; validationMessage=$message; dbRenderedCountsUnchanged=$dbUnchanged; blobCountUnchanged=$blobUnchanged." }
}

Invoke-Probe 'ST-08' {
    $owner = Login-User $clientEmail $clientPassword
    $before = Invoke-Http -Path "/klien/pra-pendaftaran/$repairCaseId" -Session $owner.Session
    $beforeIds = @([regex]::Matches($before.Content, '/klien/dokumen/([0-9]+)') | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique)
    $oldBefore = Invoke-Http -Path "/klien/dokumen/$repairOldDocumentId" -Session $owner.Session
    $form = Get-CsrfToken $owner.Session "/klien/catatan-verifikasi/$repairNoteId/perbaikan"
    $upload = Invoke-MultipartHttp -Path "/klien/catatan-verifikasi/$repairNoteId/perbaikan" -Session $owner.Session -Fields @{ _token=$form.Token } -FilePath $repairReplacementFile -FileField 'file' -MimeType 'application/pdf'
    $after = Invoke-Http -Path "/klien/pra-pendaftaran/$repairCaseId" -Session $owner.Session
    $afterIds = @([regex]::Matches($after.Content, '/klien/dokumen/([0-9]+)') | ForEach-Object { $_.Groups[1].Value } | Sort-Object -Unique)
    $oldAfter = Invoke-Http -Path "/klien/dokumen/$repairOldDocumentId" -Session $owner.Session
    $newIds = @($afterIds | Where-Object { $_ -notin $beforeIds })
    $preserved = $oldBefore.StatusCode -eq 200 -and $oldAfter.StatusCode -eq 200 -and $afterIds -contains [string] $repairOldDocumentId
    $created = $upload.StatusCode -in 302,303 -and $newIds.Count -ge 1 -and $after.Content -match '(?i)(menunggu.verifikasi.ulang|dokumen.*terkirim|sudah.diperbaiki)'
    if ($preserved -and $created) { Set-Result ST-08 PASS "Replacement created ($($newIds.Count) new document link); old document remained accessible to owner; case entered the re-verification flow." }
    else { Set-Result ST-08 FAIL "oldPreserved=$preserved; replacementCreated=$created; uploadHttp=$($upload.StatusCode); newDocumentLinks=$($newIds.Count)." }
}

Invoke-Probe 'ST-09' {
    $storage = Invoke-Http -Path ('/storage/' + $privateDocumentPath.TrimStart('/')); $landing = Invoke-Http -Path '/'
    $safeHeaders = [ordered]@{}
    foreach ($key in $landing.Headers.Keys) { if ($key -notmatch '(?i)(cookie|authorization)') { $safeHeaders[$key] = $landing.Headers[$key] } }
    $safeHeaders | ConvertTo-Json -Depth 3 | Set-Content -LiteralPath $headerFile -Encoding utf8
    $required = @('Content-Security-Policy','Strict-Transport-Security','X-Content-Type-Options','X-Frame-Options','Referrer-Policy','Permissions-Policy','Cross-Origin-Opener-Policy','Cross-Origin-Resource-Policy')
    $missing = @($required | Where-Object { -not $landing.Headers[$_] })
    $poweredBy = [string] $landing.Headers['X-Powered-By']; $server = [string] $landing.Headers['Server']
    $pass = $storage.StatusCode -in 403,404 -and $missing.Count -eq 0 -and [string]::IsNullOrWhiteSpace($poweredBy) -and $server -notmatch '(?i)(/|\d)' -and $landing.Content -notmatch 'fonts\.googleapis\.com|fonts\.gstatic\.com'
    if ($pass) { Set-Result ST-09 PASS "Private storage blocked; required headers present; runtime versions hidden (Server=$server)." }
    else { Set-Result ST-09 FAIL "storageHttp=$($storage.StatusCode); missingHeaders=$($missing -join ','); xPoweredByPresent=$(-not [string]::IsNullOrWhiteSpace($poweredBy)); server=$server." }
}

$results | ConvertTo-Json -Depth 5 | Set-Content -LiteralPath $resultFile -Encoding utf8
Write-Log "Security retest completed. Evidence directory: $runDirectory"
$statuses = @($results.Values | ForEach-Object { $_.status })
if ($statuses -contains 'FAIL' -or $statuses -contains 'ERROR') { exit 1 }
if ($statuses -contains 'PARTIAL_PASS') { exit 2 }
exit 0
