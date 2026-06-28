<?php

namespace Database\Factories;

use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RiwayatStatus>
 */
class RiwayatStatusFactory extends Factory
{
    protected $model = RiwayatStatus::class;

    public function definition(): array
    {
        return [
            "id_pendaftaran" => PraPendaftaranPerkara::factory(),
            "id_user" => User::factory()->klien(),
            "status" => "menunggu_verifikasi",
            "keterangan" => fake()->sentence(),
        ];
    }
}
