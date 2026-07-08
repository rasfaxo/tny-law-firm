<?php

namespace Tests\Feature\StafLegal;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class VerifikasiBerkasTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_staf_legal_can_verify_pengajuan_as_berkas_lengkap(): void
    {
        $stafLegal = $this->createStafLegal();
        $pengajuan = $this->createPengajuan();
        $dokumen = $this->createDokumen($pengajuan);

        $this->actingAs($stafLegal)
            ->post(route("staf-legal.verifikasi-berkas.store", $pengajuan), [
                "status_verifikasi" => "berkas_lengkap",
                "dokumen" => [
                    $dokumen->id_dokumen => ["status_dokumen" => "valid"],
                ],
            ])
            ->assertRedirect(route("staf-legal.verifikasi-berkas.riwayat"));

        $this->assertDatabaseHas("pra_pendaftaran_perkara", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "status_pengajuan" => "berkas_lengkap",
        ]);
        $this->assertDatabaseHas("dokumen_perkara", [
            "id_dokumen" => $dokumen->id_dokumen,
            "status_dokumen" => "valid",
        ]);
        $this->assertDatabaseHas("verifikasi_berkas", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_user" => $stafLegal->id_user,
            "status_verifikasi" => "berkas_lengkap",
        ]);
        $this->assertDatabaseHas("riwayat_status", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_user" => $stafLegal->id_user,
            "status" => "berkas_lengkap",
        ]);
    }

    public function test_staf_legal_can_verify_pengajuan_as_berkas_tidak_lengkap(): void
    {
        $stafLegal = $this->createStafLegal();
        $pengajuan = $this->createPengajuan();
        $dokumen = $this->createDokumen($pengajuan);

        $this->actingAs($stafLegal)
            ->post(route("staf-legal.verifikasi-berkas.store", $pengajuan), [
                "status_verifikasi" => "berkas_tidak_lengkap",
                "catatan_umum" => "Berkas perlu diperbaiki.",
                "dokumen" => [
                    $dokumen->id_dokumen => [
                        "status_dokumen" => "perlu_perbaikan",
                        "catatan" => "File kurang jelas.",
                    ],
                ],
            ])
            ->assertRedirect(route("staf-legal.verifikasi-berkas.riwayat"));

        $this->assertDatabaseHas("pra_pendaftaran_perkara", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "status_pengajuan" => "berkas_tidak_lengkap",
        ]);
        $this->assertDatabaseHas("dokumen_perkara", [
            "id_dokumen" => $dokumen->id_dokumen,
            "status_dokumen" => "perlu_perbaikan",
        ]);
        $this->assertDatabaseHas("catatan_verifikasi", [
            "id_dokumen" => $dokumen->id_dokumen,
            "isi_catatan" => "File kurang jelas.",
            "status_perbaikan" => "belum_diperbaiki",
        ]);
    }

    public function test_klien_cannot_access_verification_route(): void
    {
        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien);

        $this->actingAs($klien)
            ->post(route("staf-legal.verifikasi-berkas.store", $pengajuan), [
                "status_verifikasi" => "berkas_lengkap",
            ])
            ->assertForbidden();
    }
}
