<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class SelesaikanKonsultasiTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_admin_can_complete_confirmed_active_booking(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createBookingAktif(null, null, null, [
            "status_konfirmasi_konsultasi" => "terkonfirmasi",
            "dikonfirmasi_pada" => now(),
            "id_admin_konfirmasi" => $admin->id_user,
        ]);

        $this->actingAs($admin)
            ->patch(route("admin.booking-konsultasi.selesai", $booking))
            ->assertRedirect(route("admin.booking-konsultasi.show", $booking));

        $this->assertDatabaseHas("booking_konsultasi", [
            "id_booking" => $booking->id_booking,
            "status_booking" => "selesai",
        ]);
        $this->assertDatabaseHas("pra_pendaftaran_perkara", [
            "id_pendaftaran" => $booking->id_pendaftaran,
            "status_pengajuan" => "selesai",
        ]);
        $this->assertDatabaseHas("jadwal_konsultasi", [
            "id_jadwal" => $booking->id_jadwal,
            "status_slot" => "terisi",
        ]);
        $this->assertDatabaseHas("riwayat_status", [
            "id_pendaftaran" => $booking->id_pendaftaran,
            "id_user" => $admin->id_user,
            "status" => "selesai",
        ]);
    }

    public function test_unconfirmed_booking_cannot_be_completed(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createBookingAktif();

        $this->actingAs($admin)
            ->from(route("admin.booking-konsultasi.show", $booking))
            ->patch(route("admin.booking-konsultasi.selesai", $booking))
            ->assertSessionHas("error");

        $this->assertSame("aktif", $booking->fresh()->status_booking);
    }

    public function test_booking_with_pending_reschedule_cannot_be_completed(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createBookingAktif(null, null, null, [
            "status_konfirmasi_konsultasi" => "terkonfirmasi",
            "dikonfirmasi_pada" => now(),
            "id_admin_konfirmasi" => $admin->id_user,
        ]);
        $this->createReschedulePending($booking);

        $this->actingAs($admin)
            ->from(route("admin.booking-konsultasi.show", $booking))
            ->patch(route("admin.booking-konsultasi.selesai", $booking))
            ->assertSessionHas("error");

        $this->assertSame("aktif", $booking->fresh()->status_booking);
    }

    public function test_cancelled_or_finished_booking_cannot_be_completed_again(): void
    {
        $admin = $this->createAdmin();
        $cancelled = $this->createBookingAktif(null, null, null, [
            "status_booking" => "dibatalkan",
            "status_konfirmasi_konsultasi" => "terkonfirmasi",
        ]);
        $finished = $this->createBookingAktif(null, null, null, [
            "status_booking" => "selesai",
            "status_konfirmasi_konsultasi" => "terkonfirmasi",
        ]);

        $this->actingAs($admin)
            ->from(route("admin.booking-konsultasi.show", $cancelled))
            ->patch(route("admin.booking-konsultasi.selesai", $cancelled))
            ->assertSessionHas("error");

        $this->actingAs($admin)
            ->from(route("admin.booking-konsultasi.show", $finished))
            ->patch(route("admin.booking-konsultasi.selesai", $finished))
            ->assertSessionHas("error");
    }
}
