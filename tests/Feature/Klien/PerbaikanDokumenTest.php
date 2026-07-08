<?php

namespace Tests\Feature\Klien;

use App\Models\DokumenPerkara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class PerbaikanDokumenTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_klien_can_upload_replacement_document_for_repair_note(): void
    {
        // CRIT-01: dokumen perkara disimpan di disk "local" (private),
        // bukan disk "public".
        Storage::fake("local");

        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien, [
            "status_pengajuan" => "berkas_tidak_lengkap",
        ]);
        $dokumenLama = $this->createDokumen($pengajuan, [
            "nama_dokumen" => "KTP",
            "jenis_dokumen" => "identitas",
            "status_dokumen" => "perlu_perbaikan",
        ]);
        $verifikasi = $this->createVerifikasi($pengajuan, null, [
            "status_verifikasi" => "berkas_tidak_lengkap",
        ]);
        $catatan = $this->createCatatan($verifikasi, $dokumenLama, [
            "status_perbaikan" => "belum_diperbaiki",
        ]);

        $this->actingAs($klien)
            ->post(route("klien.perbaikan-dokumen.store", $catatan), [
                "file" => UploadedFile::fake()->create("ktp-baru.pdf", 100, "application/pdf"),
            ])
            ->assertRedirect(route("klien.pra-pendaftaran.show", $pengajuan));

        $this->assertDatabaseHas("dokumen_perkara", [
            "id_dokumen" => $dokumenLama->id_dokumen,
            "status_dokumen" => "diganti",
        ]);
        $this->assertDatabaseHas("dokumen_perkara", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "nama_dokumen" => "KTP",
            "jenis_dokumen" => "identitas",
            "status_dokumen" => "terkirim",
        ]);
        $this->assertDatabaseHas("catatan_verifikasi", [
            "id_catatan" => $catatan->id_catatan,
            "status_perbaikan" => "sudah_diperbaiki",
        ]);
        $this->assertDatabaseHas("pra_pendaftaran_perkara", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "status_pengajuan" => "menunggu_verifikasi_ulang",
        ]);
        $this->assertDatabaseHas("riwayat_status", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_user" => $klien->id_user,
            "status" => "menunggu_verifikasi_ulang",
        ]);

        $dokumenBaru = DokumenPerkara::query()
            ->where("id_pendaftaran", $pengajuan->id_pendaftaran)
            ->where("status_dokumen", "terkirim")
            ->firstOrFail();

        Storage::disk("local")->assertExists($dokumenBaru->file_path);
    }

    public function test_klien_cannot_repair_other_clients_document(): void
    {
        Storage::fake("local");

        $klienA = $this->createKlien();
        $klienB = $this->createKlien();
        $pengajuanB = $this->createPengajuan($klienB, [
            "status_pengajuan" => "berkas_tidak_lengkap",
        ]);
        $dokumenB = $this->createDokumen($pengajuanB, [
            "status_dokumen" => "perlu_perbaikan",
        ]);
        $verifikasiB = $this->createVerifikasi($pengajuanB, null, [
            "status_verifikasi" => "berkas_tidak_lengkap",
        ]);
        $catatanB = $this->createCatatan($verifikasiB, $dokumenB);

        $this->actingAs($klienA)
            ->post(route("klien.perbaikan-dokumen.store", $catatanB), [
                "file" => UploadedFile::fake()->create("dokumen.pdf", 100, "application/pdf"),
            ])
            ->assertForbidden();
    }
}
