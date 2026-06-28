<?php

namespace Database\Factories;

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
            "nama" => fake()->name(),
            "email" => fake()->unique()->safeEmail(),
            "password" => (static::$password ??= Hash::make("password")),
            "role" => "klien",
            "no_telepon" => fake()->optional()->phoneNumber(),
            "status_akun" => "aktif",
        ];
    }

    public function admin(): static
    {
        return $this->state(fn(): array => ["role" => "admin"]);
    }

    public function stafLegal(): static
    {
        return $this->state(fn(): array => ["role" => "staf_legal"]);
    }

    public function klien(): static
    {
        return $this->state(fn(): array => ["role" => "klien"]);
    }

    public function nonaktif(): static
    {
        return $this->state(fn(): array => ["status_akun" => "nonaktif"]);
    }
}
