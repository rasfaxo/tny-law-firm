<?php

namespace Tests\Feature;

use App\Models\PrivacyConsent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrivacyConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_unapproved_policy_is_clearly_marked_as_draft_and_cannot_be_accepted(): void
    {
        config()->set('privacy.ready', false);
        $user = User::factory()->create();

        $this->get(route('privacy.policy'))
            ->assertOk()
            ->assertSee('USULAN—WAJIB DISETUJUI FIRMA')
            ->assertSee('Masa layanan + 5 tahun');

        $this->actingAs($user)
            ->post(route('privacy.consent.store'), ['privacy_consent' => '1'])
            ->assertServiceUnavailable();

        $this->assertDatabaseCount('privacy_consents', 0);
    }

    public function test_verified_client_without_current_consent_is_redirected_to_consent_page(): void
    {
        $user = User::factory()->create();
        PrivacyConsent::query()->where('id_user', $user->getKey())->delete();

        $this->actingAs($user)
            ->get(route('klien.dashboard'))
            ->assertRedirect(route('privacy.consent.show'));
    }

    public function test_verified_client_can_accept_current_policy_once(): void
    {
        $user = User::factory()->create();
        PrivacyConsent::query()->where('id_user', $user->getKey())->delete();

        $this->actingAs($user)
            ->post(route('privacy.consent.store'), ['privacy_consent' => '1'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('privacy_consents', [
            'id_user' => $user->getKey(),
            'policy_version' => config('privacy.policy_version'),
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'id_actor' => $user->getKey(),
            'event' => 'privacy.consent.accepted',
        ]);

        $this->actingAs($user)
            ->post(route('privacy.consent.store'), ['privacy_consent' => '1'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseCount('privacy_consents', 1);
    }

    public function test_new_policy_version_requires_new_consent(): void
    {
        $user = User::factory()->create();
        config()->set('privacy.policy_version', 'test-v2');

        $this->actingAs($user)
            ->get(route('klien.dashboard'))
            ->assertRedirect(route('privacy.consent.show'));
    }
}
