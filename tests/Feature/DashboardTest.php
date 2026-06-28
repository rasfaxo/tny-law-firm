<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_each_role_can_open_its_dashboard(): void
    {
        $this->actingAs($this->createAdmin())
            ->get(route("admin.dashboard"))
            ->assertOk();

        $this->actingAs($this->createStafLegal())
            ->get(route("staf-legal.dashboard"))
            ->assertOk();

        $this->actingAs($this->createKlien())
            ->get(route("klien.dashboard"))
            ->assertOk();
    }

    public function test_klien_dashboard_only_displays_owned_data(): void
    {
        $klienA = $this->createKlien();
        $klienB = $this->createKlien();

        $owned = $this->createPengajuan($klienA, ["judul_perkara" => "Pengajuan A"]);
        $other = $this->createPengajuan($klienB, ["judul_perkara" => "Pengajuan B"]);

        $this->actingAs($klienA)
            ->get(route("klien.dashboard"))
            ->assertOk()
            ->assertViewHas("pengajuanTerbaru", function ($pengajuan) use ($owned, $other): bool {
                $ids = $pengajuan->pluck("id_pendaftaran");

                return $ids->contains($owned->id_pendaftaran) &&
                    !$ids->contains($other->id_pendaftaran);
            });
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        $this->get(route("dashboard"))->assertRedirect(route("login"));
        $this->get(route("klien.dashboard"))->assertRedirect(route("login"));
    }
}
