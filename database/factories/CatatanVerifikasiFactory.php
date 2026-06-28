<?php

namespace Database\Factories;

use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\VerifikasiBerkas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CatatanVerifikasi>
 */
class CatatanVerifikasiFactory extends Factory
{
    protected $model = CatatanVerifikasi::class;

    public function definition(): array
    {
        return [
            "id_verifikasi" => VerifikasiBerkas::factory(),
            "id_dokumen" => DokumenPerkara::factory(),
            "isi_catatan" => fake()->sentence(),
            "status_perbaikan" => "belum_diperbaiki",
        ];
    }
}
