<?php

namespace Database\Factories;

use App\Models\ProfilKlien;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProfilKlien>
 */
class ProfilKlienFactory extends Factory
{
    protected $model = ProfilKlien::class;

    public function definition(): array
    {
        return [
            "id_user" => User::factory()->klien(),
            "alamat" => fake()->address(),
            "jenis_kelamin" => fake()->randomElement(["laki-laki", "perempuan"]),
            "pekerjaan" => fake()->jobTitle(),
            "no_identitas" => fake()->numerify("################"),
        ];
    }
}
