<?php

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_application_responses_include_the_hardening_headers(): void
    {
        config()->set('security.csp_report_only', false);

        $response = $this->get('/');

        $response
            ->assertOk()
            ->assertHeader('Content-Security-Policy')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'DENY')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');

        $policy = $response->headers->get('Content-Security-Policy');

        $this->assertIsString($policy);
        $this->assertStringContainsString("script-src 'self'", $policy);
        $this->assertStringContainsString("style-src 'self'", $policy);
        $this->assertStringNotContainsString("'unsafe-inline'", $policy);
        $this->assertStringNotContainsString("'unsafe-eval'", $policy);
    }

    public function test_report_only_mode_is_available_for_the_strict_csp_migration(): void
    {
        config()->set('security.csp_report_only', true);

        $this->get('/')
            ->assertOk()
            ->assertHeader('Content-Security-Policy-Report-Only');
    }

    public function test_no_proxy_mode_does_not_trust_forged_forwarded_https_header(): void
    {
        config()->set('security.trusted_proxy_mode', 'none');
        config()->set('security.trusted_proxies', []);

        $this->withHeader('X-Forwarded-Proto', 'https')
            ->get('http://localhost')
            ->assertOk()
            ->assertHeaderMissing('Strict-Transport-Security');
    }
}
