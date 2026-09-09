[CmdletBinding()]
param(
    [Parameter(Mandatory)] [string] $ArtifactDirectory,
    [Parameter(Mandatory)] [string] $EvidenceDirectory
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$artifactRoot = (Resolve-Path -LiteralPath $ArtifactDirectory).Path
$checksumPath = Join-Path $artifactRoot 'rumahweb-sha256.txt'
$manifestPath = Join-Path $artifactRoot 'rumahweb-manifest.json'
foreach ($path in @($checksumPath, $manifestPath)) { if (-not (Test-Path -LiteralPath $path -PathType Leaf)) { throw "Missing artifact metadata: $path" } }

$manifest = Get-Content -LiteralPath $manifestPath -Raw | ConvertFrom-Json
$expectedCommit = '3b0ae82'
if ([string]::IsNullOrWhiteSpace([string] $manifest.commit) -or -not ([string] $manifest.commit).StartsWith($expectedCommit)) {
    throw "Manifest commit is not v1.0.0 commit $expectedCommit."
}
$results = [System.Collections.Generic.List[object]]::new()
foreach ($line in Get-Content -LiteralPath $checksumPath) {
    if ($line -notmatch '^([0-9a-fA-F]{64})\s+\*?(.+)$') { throw "Invalid checksum line: $line" }
    $expected = $Matches[1].ToLowerInvariant(); $name = $Matches[2].Trim(); $path = Join-Path $artifactRoot $name
    if (-not (Test-Path -LiteralPath $path -PathType Leaf)) { throw "Missing artifact file: $name" }
    $actual = (Get-FileHash -LiteralPath $path -Algorithm SHA256).Hash.ToLowerInvariant()
    $results.Add([pscustomobject]@{ file=$name; expected_sha256=$expected; actual_sha256=$actual; match=($actual -eq $expected) })
}
if ($results.Count -ne 2 -or @($results | Where-Object { -not $_.match }).Count -gt 0) { throw 'Artifact checksum validation failed.' }
New-Item -ItemType Directory -Force -Path $EvidenceDirectory | Out-Null
$reportPath = Join-Path $EvidenceDirectory 'release-artifact-verification.json'
$destChecksum = Join-Path $EvidenceDirectory 'rumahweb-sha256.txt'
$destManifest = Join-Path $EvidenceDirectory 'rumahweb-manifest.json'
if (@(@($reportPath,$destChecksum,$destManifest) | Where-Object { Test-Path -LiteralPath $_ }).Count -gt 0) { throw 'Artifact verification evidence already exists; do not overwrite it.' }
$report = [ordered]@{
    verified_at = (Get-Date).ToString('o'); expected_tag = 'v1.0.0'; expected_commit_prefix = $expectedCommit
    manifest = $manifest; checksums = $results
}
$report | ConvertTo-Json -Depth 6 | Set-Content -LiteralPath $reportPath -Encoding utf8
Copy-Item -LiteralPath $checksumPath -Destination $destChecksum
Copy-Item -LiteralPath $manifestPath -Destination $destManifest
Write-Host 'v1.0.0 artifact manifest and both SHA-256 checksums are valid.'
