<?php

namespace Database\Factories;

use App\Models\KategoriPerkara;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KategoriPerkara>
 */
class KategoriPerkaraFactory extends Factory
{
    protected $model = KategoriPerkara::class;

    public function definition(): array
    {
        return [
            "nama_kategori" => "Perkara " . fake()->unique()->word(),
            "deskripsi" => fake()->sentence(),
        ];
    }
}
