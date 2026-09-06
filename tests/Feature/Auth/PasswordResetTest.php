<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\QueuedResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, QueuedResetPasswordNotification::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, QueuedResetPasswordNotification::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, QueuedResetPasswordNotification::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }

    public function test_password_reset_response_does_not_reveal_whether_account_exists(): void
    {
        Notification::fake();
        $known = User::factory()->create();

        $knownResponse = $this->post('/forgot-password', ['email' => $known->email]);
        $unknownResponse = $this->post('/forgot-password', ['email' => 'tidak-ada@example.test']);

        $message = 'Jika alamat email terdaftar, tautan pengaturan ulang kata sandi akan dikirim.';

        $knownResponse->assertSessionHas('status', $message);
        $unknownResponse->assertSessionHas('status', $message);
    }

    public function test_public_password_reset_email_is_only_sent_to_active_clients(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();

        $this->post('/forgot-password', ['email' => $admin->email])
            ->assertSessionHas('status');

        Notification::assertNothingSent();
    }
}
