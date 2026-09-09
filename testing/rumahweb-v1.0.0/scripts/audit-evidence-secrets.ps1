[CmdletBinding()]
param([Parameter(Mandatory)] [string] $RunDirectory)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$resolved = (Resolve-Path -LiteralPath $RunDirectory).Path
$sensitiveNames = @(
    'RETEST_BASIC_PASSWORD','JMETER_CLIENT_PASSWORD','JMETER_LEGAL_PASSWORD',
    'SECURITY_CLIENT_PASSWORD','SECURITY_OTHER_CLIENT_PASSWORD','SECURITY_LEGAL_PASSWORD','SECURITY_ADMIN_PASSWORD',
    'SECURITY_AZURE_SAS'
)
$sensitiveValues = @($sensitiveNames | ForEach-Object { [Environment]::GetEnvironmentVariable($_) } | Where-Object { $_ -and $_.Length -ge 8 } | Sort-Object -Unique)
$textExtensions = @('.json','.csv','.jtl','.log','.txt','.html','.xml','.yaml','.yml','.md')
$findings = [System.Collections.Generic.List[string]]::new()
foreach ($file in Get-ChildItem -LiteralPath $resolved -Recurse -File) {
    if ($file.Extension.ToLowerInvariant() -notin $textExtensions) { continue }
    $content = Get-Content -LiteralPath $file.FullName -Raw -ErrorAction SilentlyContinue
    if ([string]::IsNullOrEmpty($content)) { continue }
    foreach ($value in $sensitiveValues) {
        if ($content.Contains($value)) { $findings.Add("$($file.FullName): exact configured secret value") }
    }
    if ($content -match '(?im)(authorization\s*[:=]\s*(basic|bearer)|(?:^|[?&])sig=|xsrf-token=|laravel_session=|tny-law-firm-session=|name="_token"\s+value=")') {
        $findings.Add("$($file.FullName): credential/token pattern")
    }
}
if ($findings.Count -gt 0) {
    $findings | ForEach-Object { Write-Error $_ }
    throw 'Evidence secret audit failed. Redact or remove affected new evidence before finalization.'
}
Write-Host "Evidence secret audit passed for $resolved"

