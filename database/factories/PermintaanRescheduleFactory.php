<?php

namespace Database\Factories;

use App\Models\BookingKonsultasi;
use App\Models\PermintaanReschedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PermintaanReschedule>
 */
class PermintaanRescheduleFactory extends Factory
{
    protected $model = PermintaanReschedule::class;

    public function definition(): array
    {
        return [
            "id_booking" => BookingKonsultasi::factory(),
            "id_user" => User::factory()->klien(),
            "alasan_reschedule" => fake()->sentence(),
            "preferensi_jadwal" => fake()->optional()->sentence(),
            "preferensi_metode" => fake()->optional()->randomElement(["online", "offline"]),
            "status_reschedule" => "menunggu_persetujuan",
            "id_jadwal_baru" => null,
            "id_booking_baru" => null,
            "catatan_admin" => null,
            "tanggal_pengajuan" => now(),
            "tanggal_keputusan" => null,
        ];
    }
}
