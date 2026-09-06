<?php

namespace Database\Factories;

use App\Models\PrivacyConsent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => (static::$password ??= Hash::make('password')),
            'role' => 'klien',
            'no_telepon' => fake()->optional()->phoneNumber(),
            'status_akun' => 'aktif',
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (User $user): void {
            $version = config('privacy.policy_version');

            if ($user->isKlien() && config('privacy.ready') && is_string($version) && $version !== '') {
                PrivacyConsent::firstOrCreate(
                    ['id_user' => $user->id_user, 'policy_version' => $version],
                    ['agreed_at' => now()],
                );
            }
        });
    }

    public function unverified(): static
    {
        return $this->state(fn (): array => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn (): array => ['role' => 'admin']);
    }

    public function stafLegal(): static
    {
        return $this->state(fn (): array => ['role' => 'staf_legal']);
    }

    public function klien(): static
    {
        return $this->state(fn (): array => ['role' => 'klien']);
    }

    public function nonaktif(): static
    {
        return $this->state(fn (): array => ['status_akun' => 'nonaktif']);
    }
}
