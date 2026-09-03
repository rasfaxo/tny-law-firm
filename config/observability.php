<?php

return [
    // Enable only for a bounded staging diagnostic run. This prevents routine
    // request logging from becoming an application workload itself.
    'enabled' => env('PERFORMANCE_TELEMETRY', false),
    'channel' => env('PERFORMANCE_TELEMETRY_CHANNEL', 'stack'),
    'slow_query_ms' => (int) env('PERFORMANCE_SLOW_QUERY_MS', 250),
];
