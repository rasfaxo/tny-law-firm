[CmdletBinding()]
param([Parameter(Mandatory)] [string] $RunDirectory)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$reportPath = Join-Path $RunDirectory 'zap\zap-report.json'
if (-not (Test-Path -LiteralPath $reportPath -PathType Leaf)) { throw "ZAP JSON report not found: $reportPath" }
$report = Get-Content -LiteralPath $reportPath -Raw | ConvertFrom-Json
$alerts = [System.Collections.Generic.List[object]]::new()
foreach ($site in @($report.site)) {
    foreach ($alert in @($site.alerts)) {
        $alerts.Add([pscustomobject][ordered]@{
            plugin_id = [string] $alert.pluginid
            alert = [string] $alert.alert
            risk_code = [int] $alert.riskcode
            risk_description = [string] $alert.riskdesc
            confidence = [string] $alert.confidence
            instance_count = @($alert.instances).Count
            manual_verification_status = 'UNVERIFIED'
        })
    }
}
$summary = [ordered]@{
    generated_at = (Get-Date).ToString('o')
    automated_alerts_are_confirmed_vulnerabilities = $false
    high = @($alerts | Where-Object risk_code -eq 3).Count
    medium = @($alerts | Where-Object risk_code -eq 2).Count
    low = @($alerts | Where-Object risk_code -eq 1).Count
    informational = @($alerts | Where-Object risk_code -eq 0).Count
    alerts = $alerts
}
$summary | ConvertTo-Json -Depth 7 | Set-Content -LiteralPath (Join-Path $RunDirectory 'zap\zap-alert-summary-unverified.json') -Encoding utf8
$summary | Select-Object high, medium, low, informational | Format-List
Write-Host 'All alerts remain UNVERIFIED until manual reproduction and source/config mapping are documented.'

