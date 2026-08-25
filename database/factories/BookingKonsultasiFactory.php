<?php

namespace Database\Factories;

use App\Models\BookingKonsultasi;
use App\Models\JadwalKonsultasi;
use App\Models\PraPendaftaranPerkara;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BookingKonsultasi>
 */
class BookingKonsultasiFactory extends Factory
{
    protected $model = BookingKonsultasi::class;

    public function definition(): array
    {
        return [
            "id_pendaftaran" => PraPendaftaranPerkara::factory([
                "status_pengajuan" => "jadwal_dipilih",
            ]),
            "id_jadwal" => JadwalKonsultasi::factory(["status_slot" => "terisi"]),
            "id_user" => User::factory()->klien(),
            "status_booking" => "aktif",
            "tanggal_booking" => now(),
            "metode_konsultasi" => "online",
            "status_konfirmasi_konsultasi" => "menunggu_konfirmasi",
            "link_konsultasi" => null,
            "lokasi_konsultasi" => null,
            "catatan_konsultasi" => null,
            "catatan_preferensi_klien" => null,
            "dikonfirmasi_pada" => null,
            "id_admin_konfirmasi" => null,
        ];
    }
}
