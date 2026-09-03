<?php

return [
    'csp' => env(
        'SECURITY_CSP',
        "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'",
    ),
    // Remaining Blade event handlers and style attributes still require the
    // compatibility directives above. Set this to true during CSP migration
    // to inspect violations before replacing those inline behaviours.
    'csp_report_only' => env('SECURITY_CSP_REPORT_ONLY', false),
    'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
];
