<?php

namespace Database\Factories;

use App\Models\DokumenPerkara;
use App\Models\PraPendaftaranPerkara;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DokumenPerkara>
 */
class DokumenPerkaraFactory extends Factory
{
    protected $model = DokumenPerkara::class;

    public function definition(): array
    {
        return [
            "id_pendaftaran" => PraPendaftaranPerkara::factory(),
            "nama_dokumen" => "Dokumen " . fake()->word(),
            "jenis_dokumen" => fake()->randomElement(["identitas", "bukti", "surat"]),
            "file_path" => "dokumen-perkara/" . fake()->uuid() . ".pdf",
            "status_dokumen" => "terkirim",
        ];
    }
}
