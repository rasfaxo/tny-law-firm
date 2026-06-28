<?php

namespace Database\Factories;

use App\Models\JadwalKonsultasi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JadwalKonsultasi>
 */
class JadwalKonsultasiFactory extends Factory
{
    protected $model = JadwalKonsultasi::class;

    public function definition(): array
    {
        return [
            "id_user" => User::factory()->admin(),
            "tanggal" => now()->addDays(7)->toDateString(),
            "waktu_mulai" => "09:00",
            "waktu_selesai" => "10:00",
            "status_slot" => "tersedia",
        ];
    }
}
