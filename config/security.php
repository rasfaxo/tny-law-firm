<?php

$trustedProxiesRaw = trim((string) env('TRUSTED_PROXIES', ''));
$trustedProxies = array_values(array_filter(array_map('trim', explode(',', $trustedProxiesRaw))));
$trustedProxyMode = match (true) {
    strtolower($trustedProxiesRaw) === 'none' => 'none',
    in_array('*', $trustedProxies, true) => 'invalid-wildcard',
    $trustedProxiesRaw !== '' => 'list',
    default => 'unset',
};

return [
    'trusted_hosts' => array_values(array_filter(array_map(
        static fn (string $host): string => '^'.preg_quote(trim($host), '/').'$',
        explode(',', (string) env(
            'TRUSTED_HOSTS',
            parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST) ?: 'localhost',
        )),
    ))),
    'trusted_proxy_mode' => $trustedProxyMode,
    'trusted_proxies' => $trustedProxyMode === 'list'
        ? $trustedProxies
        : [],
    'csp' => env(
        'SECURITY_CSP',
        "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'none'; form-action 'self'; img-src 'self' data:; font-src 'self'; connect-src 'self'; script-src 'self'; style-src 'self'",
    ),
    'csp_report_only' => env('SECURITY_CSP_REPORT_ONLY', false),
    'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),
];
