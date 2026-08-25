<?php

namespace Database\Factories;

use App\Models\KategoriPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PraPendaftaranPerkara>
 */
class PraPendaftaranPerkaraFactory extends Factory
{
    protected $model = PraPendaftaranPerkara::class;

    public function definition(): array
    {
        return [
            "id_user" => User::factory()->klien(),
            "id_kategori" => KategoriPerkara::factory(),
            "judul_perkara" => fake()->sentence(4),
            "kronologi" => fake()->paragraph(),
            "status_pengajuan" => "menunggu_verifikasi",
            "tanggal_pengajuan" => now(),
        ];
    }
}
