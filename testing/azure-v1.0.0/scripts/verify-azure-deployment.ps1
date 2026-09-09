[CmdletBinding()]
param(
    [Parameter(Mandatory)] [string] $RunDirectory,
    [string] $DeployedCommit = $env:AZURE_DEPLOYMENT_COMMIT,
    [string] $WorkflowRunUrl = $env:AZURE_WORKFLOW_RUN_URL
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$expectedCommit = '3b0ae8205b6e636df275f008162b25c6c8ec5bd1'
if ($DeployedCommit -ne $expectedCommit) {
    throw "Azure deployment must use v1.0.0 commit $expectedCommit; found '$DeployedCommit'."
}
$runUri = $null
if (
    -not [Uri]::TryCreate($WorkflowRunUrl, [UriKind]::Absolute, [ref] $runUri) -or
    $runUri.Scheme -ne 'https' -or
    $runUri.Host -ne 'github.com' -or
    $runUri.AbsolutePath -notmatch '^/rasfaxo/tny-law-firm/actions/runs/[0-9]+/?$'
) {
    throw 'AZURE_WORKFLOW_RUN_URL must be an HTTPS GitHub Actions run URL for rasfaxo/tny-law-firm.'
}
$releaseDirectory = Join-Path $RunDirectory 'release'
New-Item -ItemType Directory -Force -Path $releaseDirectory | Out-Null
$output = Join-Path $releaseDirectory 'azure-deployment-verification.json'
if (Test-Path -LiteralPath $output) { throw 'Azure deployment verification evidence already exists; do not overwrite it.' }
[ordered]@{
    verified_at = (Get-Date).ToString('o')
    environment = 'azure-app-service-staging'
    target = 'https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net'
    expected_tag = 'v1.0.0'
    deployed_commit = $DeployedCommit
    workflow_run_url = $runUri.AbsoluteUri
} | ConvertTo-Json -Depth 4 | Set-Content -LiteralPath $output -Encoding utf8
Write-Host "Azure v1.0.0 deployment evidence created: $output"
