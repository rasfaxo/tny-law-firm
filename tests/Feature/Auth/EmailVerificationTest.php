<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\QueuedVerifyEmailNotification;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('verification.notice'));
        $this->assertTrue(Route::has('verification.verify'));
        $this->assertTrue(Route::has('verification.send'));
    }

    public function test_unverified_client_is_redirected_to_verification_notice(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('klien.dashboard'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_email_can_be_verified_with_a_signed_link(): void
    {
        Event::fake();
        $user = User::factory()->unverified()->create();
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())],
        );

        $this->actingAs($user)->get($url)->assertRedirect(route('dashboard').'?verified=1');

        $this->assertNotNull($user->refresh()->email_verified_at);
        Event::assertDispatched(Verified::class);
    }

    public function test_verification_link_can_be_resent_through_the_queue(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect()
            ->assertSessionHas('status', 'verification-link-sent');

        Notification::assertSentTo($user, QueuedVerifyEmailNotification::class);
    }
}
