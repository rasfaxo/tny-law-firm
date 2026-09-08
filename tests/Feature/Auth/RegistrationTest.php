<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'klien',
            'status_akun' => 'aktif',
        ]);
    }

    public function test_registration_shows_an_indonesian_message_when_password_confirmation_differs(): void
    {
        $response = $this->from('/register')->post('/register', [
            'nama' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password-test-123!',
            'password_confirmation' => 'Password-test-456!',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'password' => 'kata sandi dan konfirmasinya tidak cocok.',
        ]);
    }

    public function test_registration_shows_an_indonesian_message_for_an_existing_email(): void
    {
        User::factory()->create(['email' => 'used@example.com']);

        $response = $this->from('/register')->post('/register', [
            'nama' => 'Test User',
            'email' => 'used@example.com',
            'password' => 'Password-test-123!',
            'password_confirmation' => 'Password-test-123!',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors([
            'email' => 'alamat email sudah digunakan.',
        ]);
    }
}
