<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class KonfirmasiKonsultasiTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_admin_can_confirm_online_booking_with_valid_link(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createBookingAktif(null, null, null, [
            "metode_konsultasi" => "online",
        ]);

        $this->actingAs($admin)
            ->patch(route("admin.booking-konsultasi.konfirmasi", $booking), [
                "link_konsultasi" => "https://meet.example.test/abc",
                "catatan_konsultasi" => "Silakan hadir tepat waktu.",
            ])
            ->assertRedirect(route("admin.booking-konsultasi.show", $booking));

        $this->assertDatabaseHas("booking_konsultasi", [
            "id_booking" => $booking->id_booking,
            "status_booking" => "aktif",
            "status_konfirmasi_konsultasi" => "terkonfirmasi",
            "link_konsultasi" => "https://meet.example.test/abc",
            "lokasi_konsultasi" => null,
            "id_admin_konfirmasi" => $admin->id_user,
        ]);
        $this->assertNotNull($booking->fresh()->dikonfirmasi_pada);
        $this->assertSame("jadwal_dipilih", $booking->praPendaftaranPerkara->fresh()->status_pengajuan);
    }

    public function test_admin_can_confirm_offline_booking_with_location(): void
    {
        $admin = $this->createAdmin();
        $booking = $this->createBookingAktif(null, null, null, [
            "metode_konsultasi" => "offline",
        ]);

        $this->actingAs($admin)
            ->patch(route("admin.booking-konsultasi.konfirmasi", $booking), [
                "lokasi_konsultasi" => "Kantor TNY Law Firm",
            ])
            ->assertRedirect(route("admin.booking-konsultasi.show", $booking));

        $this->assertDatabaseHas("booking_konsultasi", [
            "id_booking" => $booking->id_booking,
            "status_konfirmasi_konsultasi" => "terkonfirmasi",
            "link_konsultasi" => null,
            "lokasi_konsultasi" => "Kantor TNY Law Firm",
            "id_admin_konfirmasi" => $admin->id_user,
        ]);
    }

    public function test_confirmation_validation_rejects_missing_or_invalid_details(): void
    {
        $admin = $this->createAdmin();
        $onlineBooking = $this->createBookingAktif(null, null, null, [
            "metode_konsultasi" => "online",
        ]);
        $offlineBooking = $this->createBookingAktif(null, null, null, [
            "metode_konsultasi" => "offline",
        ]);

        $this->actingAs($admin)
            ->from(route("admin.booking-konsultasi.show", $onlineBooking))
            ->patch(route("admin.booking-konsultasi.konfirmasi", $onlineBooking), [])
            ->assertSessionHasErrors("link_konsultasi");

        $this->actingAs($admin)
            ->from(route("admin.booking-konsultasi.show", $onlineBooking))
            ->patch(route("admin.booking-konsultasi.konfirmasi", $onlineBooking), [
                "link_konsultasi" => "not-a-url",
            ])
            ->assertSessionHasErrors("link_konsultasi");

        $this->actingAs($admin)
            ->from(route("admin.booking-konsultasi.show", $offlineBooking))
            ->patch(route("admin.booking-konsultasi.konfirmasi", $offlineBooking), [])
            ->assertSessionHasErrors("lokasi_konsultasi");
    }
}
