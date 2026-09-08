<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_bootstrap_phase_accepts_explicit_no_proxy_configuration(): void
    {
        $this->configureReadiness();
        config()->set('app.admin.default_password', 'Bootstrap-Aman-123!');

        $this->artisan('app:production-readiness', ['--phase' => 'bootstrap'])
            ->assertSuccessful();
    }

    public function test_runtime_phase_requires_active_admin_and_removed_bootstrap_password(): void
    {
        $this->configureReadiness();
        User::factory()->admin()->create(['email' => 'admin@tnypartners.com']);

        $this->artisan('app:production-readiness', ['--phase' => 'runtime'])
            ->assertSuccessful();
    }

    public function test_runtime_phase_fails_while_privacy_policy_is_not_approved(): void
    {
        $this->configureReadiness();
        config()->set('privacy.ready', false);
        User::factory()->admin()->create(['email' => 'admin@tnypartners.com']);

        $this->artisan('app:production-readiness', ['--phase' => 'runtime'])
            ->assertFailed();
    }

    public function test_external_and_storage_round_trip_checks_are_isolated(): void
    {
        $this->configureReadiness();
        config()->set('app.admin.default_password', 'Bootstrap-Aman-123!');
        Storage::fake('azure');
        $this->artisan('app:production-readiness', [
            '--phase' => 'bootstrap',
            '--external' => true,
            '--storage-roundtrip' => true,
        ])->assertSuccessful();

        $this->assertSame([], Storage::disk('azure')->allFiles());
    }

    private function configureReadiness(): void
    {
        config()->set([
            'app.debug' => false,
            'app.url' => 'https://tnypartners.com',
            'app.timezone' => 'Asia/Jakarta',
            'app.locale' => 'id',
            'app.admin.name' => 'Admin TNY',
            'app.admin.email' => 'admin@tnypartners.com',
            'app.admin.default_password' => null,
            'session.driver' => 'database',
            'queue.default' => 'database',
            'mail.default' => 'resend',
            'mail.from.address' => 'no-reply@tnypartners.com',
            'mail.reply_to.address' => 'tny.partnerhukum@gmail.com',
            'services.resend.key' => 're_test_key',
            'filesystems.document_disk' => 'azure',
            'filesystems.disks.azure.connection_string' => 'UseDevelopmentStorage=true',
            'filesystems.disks.azure.container' => 'release-gate-test',
            'filesystems.disks.azure.prefix' => 'release-gate/rumahweb/test',
            'security.trusted_hosts' => ['^tnypartners\.com$', '^www\.tnypartners\.com$'],
            'security.trusted_proxy_mode' => 'none',
            'security.trusted_proxies' => [],
            'privacy.ready' => true,
            'privacy.policy_version' => 'test-v1',
            'privacy.effective_date' => '2026-09-06',
        ]);
    }
}
