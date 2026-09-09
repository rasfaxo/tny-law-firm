[CmdletBinding()]
param([Parameter(Mandatory)] [string] $RunDirectory)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$resolved = (Resolve-Path -LiteralPath $RunDirectory).Path
$manifest = Join-Path $resolved 'EVIDENCE_INTEGRITY_MANIFEST.sha256'
if (Test-Path -LiteralPath $manifest) { throw 'Evidence manifest already exists; do not overwrite immutable evidence.' }
& (Join-Path $PSScriptRoot 'audit-evidence-secrets.ps1') -RunDirectory $resolved
$lines = Get-ChildItem -LiteralPath $resolved -Recurse -File |
    Where-Object { $_.FullName -ne $manifest } |
    Sort-Object FullName |
    ForEach-Object {
        $relative = $_.FullName.Substring($resolved.Length).TrimStart('\').Replace('\','/')
        $hash = (Get-FileHash -LiteralPath $_.FullName -Algorithm SHA256).Hash.ToLowerInvariant()
        "$hash  $relative"
    }
$lines | Set-Content -LiteralPath $manifest -Encoding ascii
Write-Host "Evidence manifest created: $manifest"
