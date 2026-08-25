<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class JadwalKonsultasiTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_admin_can_create_jadwal_konsultasi_with_default_available_status(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->post(route("admin.jadwal-konsultasi.store"), [
                "tanggal" => now()->addDays(5)->toDateString(),
                "waktu_mulai" => "09:00",
                "waktu_selesai" => "10:00",
            ])
            ->assertRedirect();

        $this->assertDatabaseHas("jadwal_konsultasi", [
            "id_user" => $admin->id_user,
            "status_slot" => "tersedia",
            "waktu_mulai" => "09:00",
            "waktu_selesai" => "10:00",
        ]);
    }

    public function test_overlapping_jadwal_is_rejected(): void
    {
        $admin = $this->createAdmin();
        $tanggal = now()->addDays(6)->toDateString();

        $this->createJadwalTersedia($admin, [
            "tanggal" => $tanggal,
            "waktu_mulai" => "09:00",
            "waktu_selesai" => "10:00",
        ]);

        $this->actingAs($admin)
            ->from(route("admin.jadwal-konsultasi.create"))
            ->post(route("admin.jadwal-konsultasi.store"), [
                "tanggal" => $tanggal,
                "waktu_mulai" => "09:30",
                "waktu_selesai" => "10:30",
            ])
            ->assertSessionHasErrors("waktu_mulai");
    }

    public function test_filled_jadwal_cannot_be_edited(): void
    {
        $admin = $this->createAdmin();
        $jadwal = $this->createJadwalTersedia($admin, ["status_slot" => "terisi"]);

        $this->actingAs($admin)
            ->get(route("admin.jadwal-konsultasi.edit", $jadwal))
            ->assertRedirect(route("admin.jadwal-konsultasi.show", $jadwal));

        $this->actingAs($admin)
            ->from(route("admin.jadwal-konsultasi.show", $jadwal))
            ->put(route("admin.jadwal-konsultasi.update", $jadwal), [
                "tanggal" => now()->addDays(8)->toDateString(),
                "waktu_mulai" => "13:00",
                "waktu_selesai" => "14:00",
                "status_slot" => "tersedia",
            ])
            ->assertSessionHasErrors("status_slot");
    }

    public function test_non_admin_roles_cannot_access_jadwal_management(): void
    {
        $this->actingAs($this->createKlien())
            ->get(route("admin.jadwal-konsultasi.index"))
            ->assertForbidden();

        $this->actingAs($this->createStafLegal())
            ->get(route("admin.jadwal-konsultasi.index"))
            ->assertForbidden();
    }
}
