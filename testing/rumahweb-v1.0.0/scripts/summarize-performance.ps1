[CmdletBinding()]
param([Parameter(Mandatory)] [string] $RunDirectory)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$recorded = Join-Path $RunDirectory 'performance\recorded'
if (-not (Test-Path -LiteralPath $recorded -PathType Container)) { throw "Recorded JTL directory not found: $recorded" }

$mapping = @(
    [pscustomobject]@{ Id='PF-01'; Flow='general'; Label='POST Login Klien' },
    [pscustomobject]@{ Id='PF-02'; Flow='client'; Label='PF-02 - GET Form Pra-Pendaftaran' },
    [pscustomobject]@{ Id='PF-03'; Flow='client'; Label='PF-03 - POST Formulir Perkara' },
    [pscustomobject]@{ Id='PF-04'; Flow='repair'; Label='PF-04 - POST Unggah Ulang Dokumen' },
    [pscustomobject]@{ Id='PF-05'; Flow='client'; Label='PF-05 - GET Monitoring Status' },
    [pscustomobject]@{ Id='PF-06'; Flow='general'; Label='GET Klien Dashboard' },
    [pscustomobject]@{ Id='PF-07'; Flow='legal'; Label='PF-07 - POST Submit Verifikasi Berkas' }
)

$metrics = [System.Collections.Generic.List[object]]::new()
foreach ($threads in @(5,10,20)) {
    foreach ($item in $mapping) {
        $jtl = Join-Path $recorded "${threads}vu-$($item.Flow).jtl"
        if (-not (Test-Path -LiteralPath $jtl -PathType Leaf)) { throw "Missing raw JTL: $jtl" }
        $samples = @(Import-Csv -LiteralPath $jtl | Where-Object { $_.label -eq $item.Label })
        if ($samples.Count -eq 0) { throw "No samples for $($item.Id) in $jtl" }
        $elapsed = @($samples | ForEach-Object { [double] $_.elapsed } | Sort-Object)
        $p95Index = [Math]::Ceiling(0.95 * $elapsed.Count) - 1
        $starts = @($samples | ForEach-Object { [double] $_.timeStamp })
        $ends = @($samples | ForEach-Object { [double] $_.timeStamp + [double] $_.elapsed })
        $durationSeconds = ((($ends | Measure-Object -Maximum).Maximum) - (($starts | Measure-Object -Minimum).Minimum)) / 1000
        $failed = @($samples | Where-Object { $_.success -ne 'true' }).Count
        $execution = if ($samples.Count -eq $threads -and $failed -eq 0) { 'PASS' } else { 'FAIL' }
        $p95 = [Math]::Round($elapsed[$p95Index], 2)
        $metrics.Add([pscustomobject][ordered]@{
            id = $item.Id; virtual_users = $threads; sample_count = $samples.Count
            average_ms = [Math]::Round((($elapsed | Measure-Object -Average).Average), 2)
            p95_nearest_rank_ms = $p95
            minimum_ms = [Math]::Round((($elapsed | Measure-Object -Minimum).Minimum), 2)
            maximum_ms = [Math]::Round((($elapsed | Measure-Object -Maximum).Maximum), 2)
            throughput_requests_per_second = if ($durationSeconds -gt 0) { [Math]::Round($samples.Count / $durationSeconds, 4) } else { $null }
            error_rate_percent = [Math]::Round(($failed * 100.0 / $samples.Count), 2)
            execution_status = $execution
            p95_target_ms = 3000
            p95_target_status = if ($p95 -le 3000) { 'ACHIEVED' } else { 'NOT_ACHIEVED' }
            source_jtl = "performance/recorded/${threads}vu-$($item.Flow).jtl"
        })
    }
}
if ($metrics.Count -ne 21) { throw "Expected 21 metric rows, produced $($metrics.Count)." }
$outputDirectory = Join-Path $RunDirectory 'performance\metrics'
New-Item -ItemType Directory -Force -Path $outputDirectory | Out-Null
$metrics | Export-Csv -LiteralPath (Join-Path $outputDirectory 'performance-metrics.csv') -NoTypeInformation -Encoding utf8
$metrics | ConvertTo-Json -Depth 4 | Set-Content -LiteralPath (Join-Path $outputDirectory 'performance-metrics.json') -Encoding utf8
$summary = [ordered]@{
    generated_at = (Get-Date).ToString('o'); metric_rows = $metrics.Count
    execution_pass = @($metrics | Where-Object execution_status -eq PASS).Count
    execution_fail = @($metrics | Where-Object execution_status -eq FAIL).Count
    p95_achieved = @($metrics | Where-Object p95_target_status -eq ACHIEVED).Count
    p95_not_achieved = @($metrics | Where-Object p95_target_status -eq NOT_ACHIEVED).Count
}
$summary | ConvertTo-Json | Set-Content -LiteralPath (Join-Path $outputDirectory 'performance-summary.json') -Encoding utf8
$summary | Format-List
