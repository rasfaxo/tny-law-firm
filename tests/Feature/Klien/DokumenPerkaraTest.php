<?php

namespace Tests\Feature\Klien;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Concerns\CreatesTestingData;
use Tests\TestCase;

class DokumenPerkaraTest extends TestCase
{
    use CreatesTestingData;
    use RefreshDatabase;

    public function test_post_submit_additional_document_routes_are_not_registered(): void
    {
        $this->assertFalse(Route::has('klien.dokumen.create'));
        $this->assertFalse(Route::has('klien.dokumen.store'));
    }

    public function test_post_submit_additional_document_endpoint_returns_not_found(): void
    {
        $klien = $this->createKlien();
        $pengajuan = $this->createPengajuan($klien);

        $this->actingAs($klien)
            ->post("/klien/pra-pendaftaran/{$pengajuan->getKey()}/dokumen", [])
            ->assertNotFound();

        $this->assertDatabaseCount('dokumen_perkara', 0);
    }
}
