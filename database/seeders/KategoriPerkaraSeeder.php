<?php

namespace Database\Seeders;

use App\Models\KategoriPerkara;
use Illuminate\Database\Seeder;

class KategoriPerkaraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriPerkara = [
            [
                'nama_kategori' => 'Perdata',
                'deskripsi' => 'Perkara hukum perdata dan sengketa antar pihak.',
            ],
            [
                'nama_kategori' => 'Pidana',
                'deskripsi' => 'Perkara hukum pidana dan pendampingan terkait proses pidana.',
            ],
            [
                'nama_kategori' => 'Keluarga',
                'deskripsi' => 'Perkara hukum keluarga seperti perceraian, hak asuh, dan waris.',
            ],
            [
                'nama_kategori' => 'Ketenagakerjaan',
                'deskripsi' => 'Perkara hubungan kerja dan sengketa ketenagakerjaan.',
            ],
        ];

        foreach ($kategoriPerkara as $kategori) {
            KategoriPerkara::firstOrCreate(
                ['nama_kategori' => $kategori['nama_kategori']],
                ['deskripsi' => $kategori['deskripsi']]
            );
        }
    }
}
