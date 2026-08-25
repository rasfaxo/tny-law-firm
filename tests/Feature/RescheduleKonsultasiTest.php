<?php

namespace Tests\Feature;

use App\Models\BookingKonsultasi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class RescheduleKonsultasiTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_klien_can_request_reschedule_for_owned_active_booking(): void
    {
        $klien = $this->createKlien();
        $booking = $this->createBookingAktif($klien);

        $this->actingAs($klien)
            ->post(route("klien.permintaan-reschedule.store", $booking), [
                "alasan_reschedule" => "Ada agenda keluarga mendadak.",
                "preferensi_jadwal" => "Pekan depan pagi.",
                "preferensi_metode" => "offline",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("permintaan_reschedule", [
            "id_booking" => $booking->id_booking,
            "id_user" => $klien->id_user,
            "status_reschedule" => "menunggu_persetujuan",
            "preferensi_metode" => "offline",
        ]);
        $this->assertDatabaseHas("booking_konsultasi", [
            "id_booking" => $booking->id_booking,
            "status_booking" => "aktif",
        ]);
        $this->assertDatabaseHas("jadwal_konsultasi", [
            "id_jadwal" => $booking->id_jadwal,
            "status_slot" => "terisi",
        ]);
    }

    public function test_reschedule_request_requires_reason_and_blocks_duplicate_pending_request(): void
    {
        $klien = $this->createKlien();
        $booking = $this->createBookingAktif($klien);

        $this->actingAs($klien)
            ->from(route("klien.permintaan-reschedule.create", $booking))
            ->post(route("klien.permintaan-reschedule.store", $booking), [])
            ->assertSessionHasErrors("alasan_reschedule");

        $this->createReschedulePending($booking);

        $this->actingAs($klien)
            ->from(route("klien.permintaan-reschedule.create", $booking))
            ->post(route("klien.permintaan-reschedule.store", $booking), [
                "alasan_reschedule" => "Mengajukan ulang.",
            ])
            ->assertSessionHasErrors("alasan_reschedule");
    }

    public function test_klien_cannot_request_reschedule_for_other_clients_booking(): void
    {
        $klienA = $this->createKlien();
        $klienB = $this->createKlien();
        $bookingB = $this->createBookingAktif($klienB);

        $this->actingAs($klienA)
            ->post(route("klien.permintaan-reschedule.store", $bookingB), [
                "alasan_reschedule" => "Tidak berhak.",
            ])
            ->assertForbidden();
    }

    public function test_admin_can_reject_reschedule_without_changing_old_booking(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createBookingAktif();
        $reschedule = $this->createReschedulePending($booking);

        $this->actingAs($admin)
            ->patch(route("admin.permintaan-reschedule.tolak", $reschedule), [
                "catatan_admin" => "Jadwal lama tetap digunakan.",
            ])
            ->assertRedirect(route("admin.permintaan-reschedule.show", $reschedule));

        $this->assertDatabaseHas("permintaan_reschedule", [
            "id_reschedule" => $reschedule->id_reschedule,
            "status_reschedule" => "ditolak",
            "catatan_admin" => "Jadwal lama tetap digunakan.",
        ]);
        $this->assertNotNull($reschedule->fresh()->tanggal_keputusan);
        $this->assertDatabaseHas("booking_konsultasi", [
            "id_booking" => $booking->id_booking,
            "status_booking" => "aktif",
        ]);
        $this->assertDatabaseHas("jadwal_konsultasi", [
            "id_jadwal" => $booking->id_jadwal,
            "status_slot" => "terisi",
        ]);
    }

    public function test_admin_can_approve_reschedule_with_available_new_schedule(): void
    {
        $admin = $this->createAdmin();
        $bookingLama = $this->createBookingAktif(null, null, null, [
            "metode_konsultasi" => "online",
        ]);
        $reschedule = $this->createReschedulePending($bookingLama, [
            "preferensi_metode" => "offline",
        ]);
        $jadwalBaru = $this->createJadwalTersedia(null, [
            "tanggal" => now()->addDays(12)->toDateString(),
            "waktu_mulai" => "13:00",
            "waktu_selesai" => "14:00",
        ]);

        $this->actingAs($admin)
            ->patch(route("admin.permintaan-reschedule.setujui", $reschedule), [
                "id_jadwal_baru" => $jadwalBaru->id_jadwal,
                "catatan_admin" => "Disetujui.",
            ])
            ->assertRedirect(route("admin.permintaan-reschedule.show", $reschedule));

        $reschedule->refresh();
        $bookingBaru = BookingKonsultasi::query()->findOrFail($reschedule->id_booking_baru);

        $this->assertSame("disetujui", $reschedule->status_reschedule);
        $this->assertSame($jadwalBaru->id_jadwal, $reschedule->id_jadwal_baru);
        $this->assertNotNull($reschedule->tanggal_keputusan);
        $this->assertSame("dibatalkan", $bookingLama->fresh()->status_booking);
        $this->assertSame("tersedia", $bookingLama->jadwalKonsultasi->fresh()->status_slot);
        $this->assertSame("aktif", $bookingBaru->status_booking);
        $this->assertSame("offline", $bookingBaru->metode_konsultasi);
        $this->assertSame("menunggu_konfirmasi", $bookingBaru->status_konfirmasi_konsultasi);
        $this->assertSame("terisi", $jadwalBaru->fresh()->status_slot);
        $this->assertDatabaseHas("riwayat_status", [
            "id_pendaftaran" => $bookingLama->id_pendaftaran,
            "id_user" => $admin->id_user,
            "status" => "jadwal_dipilih",
        ]);
    }

    public function test_processed_reschedule_cannot_be_processed_again(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createBookingAktif();
        $reschedule = $this->createReschedulePending($booking, [
            "status_reschedule" => "ditolak",
            "catatan_admin" => "Sudah ditolak.",
            "tanggal_keputusan" => now(),
        ]);

        $this->actingAs($admin)
            ->from(route("admin.permintaan-reschedule.show", $reschedule))
            ->patch(route("admin.permintaan-reschedule.tolak", $reschedule), [
                "catatan_admin" => "Diproses lagi.",
            ])
            ->assertSessionHasErrors("catatan_admin");
    }
}
