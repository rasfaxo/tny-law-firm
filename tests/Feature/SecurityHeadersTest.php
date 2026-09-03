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
    }

    public function test_report_only_mode_is_available_for_the_strict_csp_migration(): void
    {
        config()->set('security.csp_report_only', true);

        $this->get('/')
            ->assertOk()
            ->assertHeader('Content-Security-Policy-Report-Only');
    }
}
