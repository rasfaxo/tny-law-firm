<?php

namespace Database\Factories;

use App\Models\PraPendaftaranPerkara;
use App\Models\User;
use App\Models\VerifikasiBerkas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VerifikasiBerkas>
 */
class VerifikasiBerkasFactory extends Factory
{
    protected $model = VerifikasiBerkas::class;

    public function definition(): array
    {
        return [
            "id_pendaftaran" => PraPendaftaranPerkara::factory(),
            "id_user" => User::factory()->stafLegal(),
            "status_verifikasi" => "berkas_lengkap",
            "tanggal_verifikasi" => now(),
            "catatan_umum" => null,
        ];
    }
}
