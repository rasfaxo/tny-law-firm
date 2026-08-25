<?php

namespace Tests\Feature\Klien;

use App\Models\PraPendaftaranPerkara;
use App\Models\RiwayatStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class PraPendaftaranPerkaraTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_klien_can_create_pra_pendaftaran_perkara(): void
    {
        $klien = $this->createKlien();
        $kategori = $this->createKategori();

        $response = $this->actingAs($klien)->post(route("klien.pra-pendaftaran.store"), [
            "id_kategori" => $kategori->id_kategori,
            "judul_perkara" => "Sengketa Perdata",
            "kronologi" => "Kronologi perkara untuk kebutuhan test.",
            "dokumen" => [
                [
                    "nama_dokumen" => "KTP Klien",
                    "jenis_dokumen" => "ktp",
                    "file_dokumen" => \Illuminate\Http\UploadedFile::fake()->image('ktp.jpg')->size(100),
                ],
            ],
        ]);

        $pengajuan = PraPendaftaranPerkara::query()->firstOrFail();

        $response->assertRedirect(route("klien.pra-pendaftaran.show", $pengajuan));

        $this->assertSame($klien->id_user, $pengajuan->id_user);
        $this->assertSame($kategori->id_kategori, $pengajuan->id_kategori);
        $this->assertSame("menunggu_verifikasi", $pengajuan->status_pengajuan);
        $this->assertNotNull($pengajuan->tanggal_pengajuan);

        $this->assertDatabaseHas("riwayat_status", [
            "id_pendaftaran" => $pengajuan->id_pendaftaran,
            "id_user" => $klien->id_user,
            "status" => "menunggu_verifikasi",
        ]);
    }

    public function test_klien_cannot_see_other_clients_pengajuan_detail(): void
    {
        $klienA = $this->createKlien();
        $klienB = $this->createKlien();
        $pengajuanB = $this->createPengajuan($klienB);

        $this->actingAs($klienA)
            ->get(route("klien.pra-pendaftaran.show", $pengajuanB))
            ->assertForbidden();
    }

    public function test_klien_dashboard_and_index_only_include_owned_pengajuan(): void
    {
        $klienA = $this->createKlien();
        $klienB = $this->createKlien();

        $owned = $this->createPengajuan($klienA, ["judul_perkara" => "Milik Klien A"]);
        $other = $this->createPengajuan($klienB, ["judul_perkara" => "Milik Klien B"]);

        RiwayatStatus::factory()->create([
            "id_pendaftaran" => $owned->id_pendaftaran,
            "id_user" => $klienA->id_user,
            "status" => $owned->status_pengajuan,
        ]);
        RiwayatStatus::factory()->create([
            "id_pendaftaran" => $other->id_pendaftaran,
            "id_user" => $klienB->id_user,
            "status" => $other->status_pengajuan,
        ]);

        $this->actingAs($klienA)
            ->get(route("klien.pra-pendaftaran.index"))
            ->assertOk()
            ->assertSee("Milik Klien A")
            ->assertDontSee("Milik Klien B");

        $this->actingAs($klienA)
            ->get(route("klien.dashboard"))
            ->assertOk()
            ->assertSee("Milik Klien A")
            ->assertDontSee("Milik Klien B");
    }
}
