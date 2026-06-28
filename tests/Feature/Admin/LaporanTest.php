<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_admin_can_open_all_report_pages(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)->get(route("admin.laporan.index"))->assertOk();
        $this->actingAs($admin)->get(route("admin.laporan.pra-pendaftaran"))->assertOk();
        $this->actingAs($admin)->get(route("admin.laporan.verifikasi-berkas"))->assertOk();
        $this->actingAs($admin)->get(route("admin.laporan.booking-konsultasi"))->assertOk();
        $this->actingAs($admin)->get(route("admin.laporan.reschedule-konsultasi"))->assertOk();
        $this->actingAs($admin)->get(route("admin.laporan.pengajuan-selesai"))->assertOk();
    }

    public function test_report_filters_accept_valid_input(): void
    {
        $admin = $this->createAdmin();
        $kategori = $this->createKategori();
        $this->createPengajuan(null, [
            "id_kategori" => $kategori->id_kategori,
            "status_pengajuan" => "berkas_lengkap",
            "tanggal_pengajuan" => now()->subDay(),
        ]);

        $this->actingAs($admin)
            ->get(route("admin.laporan.pra-pendaftaran", [
                "tanggal_mulai" => now()->subDays(2)->toDateString(),
                "tanggal_selesai" => now()->toDateString(),
                "status_pengajuan" => "berkas_lengkap",
                "id_kategori" => $kategori->id_kategori,
            ]))
            ->assertOk()
            ->assertSee("berkas_lengkap");
    }

    public function test_report_filters_reject_invalid_input(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->from(route("admin.laporan.pra-pendaftaran"))
            ->get(route("admin.laporan.pra-pendaftaran", [
                "tanggal_mulai" => now()->toDateString(),
                "tanggal_selesai" => now()->subDay()->toDateString(),
                "status_pengajuan" => "status_tidak_valid",
            ]))
            ->assertSessionHasErrors(["tanggal_selesai", "status_pengajuan"]);
    }

    public function test_non_admin_roles_cannot_access_reports(): void
    {
        $this->actingAs($this->createKlien())
            ->get(route("admin.laporan.index"))
            ->assertForbidden();

        $this->actingAs($this->createStafLegal())
            ->get(route("admin.laporan.index"))
            ->assertForbidden();
    }
}
