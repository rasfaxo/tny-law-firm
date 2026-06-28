<?php

namespace Tests\Feature\Klien;

use App\Models\DokumenPerkara;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class DokumenPerkaraTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_klien_can_upload_valid_document_for_owned_pengajuan(): void
    {
        Storage::fake("public");

        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien);

        $response = $this->actingAs($klien)->post(route("klien.dokumen.store", $pengajuan), [
            "nama_dokumen" => "KTP",
            "jenis_dokumen" => "identitas",
            "file" => UploadedFile::fake()->create("ktp.pdf", 100, "application/pdf"),
        ]);

        $response->assertRedirect(route("klien.pra-pendaftaran.show", $pengajuan));

        $dokumen = DokumenPerkara::query()->firstOrFail();

        $this->assertSame($pengajuan->id_pendaftaran, $dokumen->id_pendaftaran);
        $this->assertSame("terkirim", $dokumen->status_dokumen);
        $this->assertNotSame("ktp.pdf", basename($dokumen->file_path));
        Storage::disk("public")->assertExists($dokumen->file_path);
    }

    public function test_invalid_document_file_is_rejected(): void
    {
        Storage::fake("public");

        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien);

        $this->actingAs($klien)
            ->from(route("klien.dokumen.create", $pengajuan))
            ->post(route("klien.dokumen.store", $pengajuan), [
                "nama_dokumen" => "File salah",
                "jenis_dokumen" => "bukti",
                "file" => UploadedFile::fake()->create("bukti.txt", 100, "text/plain"),
            ])
            ->assertSessionHasErrors("file");

        $this->assertDatabaseCount("dokumen_perkara", 0);
    }

    public function test_document_larger_than_five_mb_is_rejected(): void
    {
        Storage::fake("public");

        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien);

        $this->actingAs($klien)
            ->from(route("klien.dokumen.create", $pengajuan))
            ->post(route("klien.dokumen.store", $pengajuan), [
                "nama_dokumen" => "File besar",
                "jenis_dokumen" => "bukti",
                "file" => UploadedFile::fake()->create("besar.pdf", 5121, "application/pdf"),
            ])
            ->assertSessionHasErrors("file");

        $this->assertDatabaseCount("dokumen_perkara", 0);
    }

    public function test_klien_cannot_upload_document_to_other_clients_pengajuan(): void
    {
        Storage::fake("public");

        $klienA = $this->createKlien();
        $klienB = $this->createKlien();
        $pengajuanB = $this->createPengajuan($klienB);

        $this->actingAs($klienA)
            ->post(route("klien.dokumen.store", $pengajuanB), [
                "nama_dokumen" => "Dokumen tidak sah",
                "jenis_dokumen" => "bukti",
                "file" => UploadedFile::fake()->create("bukti.pdf", 100, "application/pdf"),
            ])
            ->assertForbidden();

        $this->assertDatabaseCount("dokumen_perkara", 0);
    }
}
