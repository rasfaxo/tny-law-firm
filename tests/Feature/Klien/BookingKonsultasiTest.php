<?php

namespace Tests\Feature\Klien;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class BookingKonsultasiTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_klien_can_book_available_schedule_for_completed_files(): void
    {
        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien, [
            "status_pengajuan" => "berkas_lengkap",
        ]);
        $jadwal = $this->createJadwalTersedia();

        $this->actingAs($klien)
            ->post(route("klien.booking-konsultasi.store", $pengajuan), [
                "id_jadwal" => $jadwal->id_jadwal,
                "metode_konsultasi" => "online",
                "catatan_preferensi_klien" => "Lebih nyaman pagi.",
            ])
            ->assertRedirect(route("klien.pra-pendaftaran.show", $pengajuan));

        $this->assertDatabaseHas("booking_konsultasi", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_jadwal" => $jadwal->id_jadwal,
            "id_user" => $klien->id_user,
            "status_booking" => "aktif",
            "metode_konsultasi" => "online",
            "status_konfirmasi_konsultasi" => "menunggu_konfirmasi",
        ]);
        $this->assertDatabaseHas("jadwal_konsultasi", [
            "id_jadwal" => $jadwal->id_jadwal,
            "status_slot" => "terisi",
        ]);
        $this->assertDatabaseHas("pra_pendaftaran_perkara", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "status_pengajuan" => "jadwal_dipilih",
        ]);
        $this->assertDatabaseHas("riwayat_status", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_user" => $klien->id_user,
            "status" => "jadwal_dipilih",
        ]);
    }

    public function test_klien_can_book_with_offline_method(): void
    {
        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien, [
            "status_pengajuan" => "berkas_lengkap",
        ]);
        $jadwal = $this->createJadwalTersedia(null, [
            "tanggal" => now()->addDays(9)->toDateString(),
            "waktu_mulai" => "11:00",
            "waktu_selesai" => "12:00",
        ]);

        $this->actingAs($klien)
            ->post(route("klien.booking-konsultasi.store", $pengajuan), [
                "id_jadwal" => $jadwal->id_jadwal,
                "metode_konsultasi" => "offline",
            ])
            ->assertRedirect(route("klien.pra-pendaftaran.show", $pengajuan));

        $this->assertDatabaseHas("booking_konsultasi", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "metode_konsultasi" => "offline",
        ]);
    }

    public function test_klien_cannot_book_other_clients_pengajuan(): void
    {
        $klienA = $this->createKlien();
        $klienB = $this->createKlien();
        $pengajuanB = $this->createPengajuan($klienB, [
            "status_pengajuan" => "berkas_lengkap",
        ]);
        $jadwal = $this->createJadwalTersedia();

        $this->actingAs($klienA)
            ->post(route("klien.booking-konsultasi.store", $pengajuanB), [
                "id_jadwal" => $jadwal->id_jadwal,
                "metode_konsultasi" => "online",
            ])
            ->assertForbidden();
    }

    public function test_klien_cannot_book_unavailable_schedule_or_unready_pengajuan(): void
    {
        $klien = $this->createKlien();
        $pengajuanLengkap = $this->createPengajuan($klien, [
            "status_pengajuan" => "berkas_lengkap",
        ]);
        $pengajuanMenunggu = $this->createPengajuan($klien, [
            "status_pengajuan" => "menunggu_verifikasi",
        ]);
        $jadwalTerisi = $this->createJadwalTersedia(null, ["status_slot" => "terisi"]);
        $jadwalTidakAktif = $this->createJadwalTersedia(null, [
            "tanggal" => now()->addDays(10)->toDateString(),
            "waktu_mulai" => "15:00",
            "waktu_selesai" => "16:00",
            "status_slot" => "tidak_aktif",
        ]);
        $jadwalTersedia = $this->createJadwalTersedia(null, [
            "tanggal" => now()->addDays(11)->toDateString(),
            "waktu_mulai" => "08:00",
            "waktu_selesai" => "09:00",
        ]);

        $this->actingAs($klien)
            ->from(route("klien.booking-konsultasi.create", $pengajuanLengkap))
            ->post(route("klien.booking-konsultasi.store", $pengajuanLengkap), [
                "id_jadwal" => $jadwalTerisi->id_jadwal,
                "metode_konsultasi" => "online",
            ])
            ->assertSessionHasErrors("id_jadwal");

        $this->actingAs($klien)
            ->from(route("klien.booking-konsultasi.create", $pengajuanLengkap))
            ->post(route("klien.booking-konsultasi.store", $pengajuanLengkap), [
                "id_jadwal" => $jadwalTidakAktif->id_jadwal,
                "metode_konsultasi" => "offline",
            ])
            ->assertSessionHasErrors("id_jadwal");

        $this->actingAs($klien)
            ->from(route("klien.pra-pendaftaran.show", $pengajuanMenunggu))
            ->post(route("klien.booking-konsultasi.store", $pengajuanMenunggu), [
                "id_jadwal" => $jadwalTersedia->id_jadwal,
                "metode_konsultasi" => "online",
            ])
            ->assertSessionHasErrors("id_jadwal");
    }
}
