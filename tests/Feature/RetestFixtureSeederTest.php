<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RetestFixtureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

class RetestFixtureSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set([
            'app.url' => 'https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net',
            'retest.fixtures.enabled' => true,
            'retest.fixtures.confirmation' => 'SEED-AZURE-V100-RETEST-ONLY',
            'retest.fixtures.password' => 'Fixture-Test-Password-123!',
            'filesystems.document_disk' => 'azure',
            'filesystems.disks.azure.container' => 'documents',
            'filesystems.disks.azure.prefix' => 'retest/v1.0.0/tnypartners',
            'privacy.ready' => true,
            'privacy.policy_version' => 'v1.0',
        ]);

        Storage::fake('azure');
    }

    public function test_seeder_creates_complete_anonymous_fixture(): void
    {
        $this->seed(RetestFixtureSeeder::class);

        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseCount('profil_klien', 2);
        $this->assertDatabaseCount('privacy_consents', 2);
        $this->assertDatabaseCount('kategori_perkara', 1);
        $this->assertDatabaseCount('pra_pendaftaran_perkara', 2);
        $this->assertDatabaseCount('dokumen_perkara', 2);
        $this->assertDatabaseCount('verifikasi_berkas', 1);
        $this->assertDatabaseCount('catatan_verifikasi', 1);
        $this->assertDatabaseCount('riwayat_status', 3);

        $this->assertDatabaseHas('users', [
            'email' => RetestFixtureSeeder::CLIENT_A_EMAIL,
            'role' => 'klien',
            'status_akun' => 'aktif',
        ]);
        $this->assertDatabaseHas('pra_pendaftaran_perkara', [
            'judul_perkara' => RetestFixtureSeeder::REPAIR_CASE_TITLE,
            'status_pengajuan' => 'berkas_tidak_lengkap',
        ]);
        $this->assertDatabaseHas('dokumen_perkara', [
            'file_path' => RetestFixtureSeeder::REPAIR_DOCUMENT_PATH,
            'status_dokumen' => 'perlu_perbaikan',
        ]);
        $this->assertDatabaseHas('catatan_verifikasi', [
            'status_perbaikan' => 'belum_diperbaiki',
        ]);

        $storedHash = (string) User::query()
            ->where('email', RetestFixtureSeeder::CLIENT_A_EMAIL)
            ->value('password');
        $this->assertTrue(Hash::check('Fixture-Test-Password-123!', $storedHash));

        Storage::disk('azure')->assertExists(RetestFixtureSeeder::OWNERSHIP_DOCUMENT_PATH);
        Storage::disk('azure')->assertExists(RetestFixtureSeeder::REPAIR_DOCUMENT_PATH);
    }

    public function test_seeder_is_idempotent_before_fixture_is_mutated_by_tests(): void
    {
        $this->seed(RetestFixtureSeeder::class);
        $this->seed(RetestFixtureSeeder::class);

        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseCount('pra_pendaftaran_perkara', 2);
        $this->assertDatabaseCount('dokumen_perkara', 2);
        $this->assertDatabaseCount('verifikasi_berkas', 1);
        $this->assertDatabaseCount('catatan_verifikasi', 1);
        $this->assertDatabaseCount('riwayat_status', 3);
    }

    public function test_seeder_refuses_a_different_host_before_writing_anything(): void
    {
        config()->set('app.url', 'https://tnypartners.com');

        try {
            $this->seed(RetestFixtureSeeder::class);
            $this->fail('Seeder seharusnya menolak target selain Azure staging.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('APP_URL', $exception->getMessage());
        }

        $this->assertDatabaseCount('users', 0);
        Storage::disk('azure')->assertMissing(RetestFixtureSeeder::OWNERSHIP_DOCUMENT_PATH);
    }

    public function test_seeder_does_nothing_when_feature_flag_is_disabled(): void
    {
        config()->set('retest.fixtures.enabled', false);

        $this->seed(RetestFixtureSeeder::class);

        $this->assertDatabaseCount('users', 0);
        Storage::disk('azure')->assertMissing(RetestFixtureSeeder::OWNERSHIP_DOCUMENT_PATH);
    }

    public function test_production_seeder_refuses_a_database_not_named_for_retest(): void
    {
        $this->app->detectEnvironment(fn (): string => 'production');
        try {
            (new RetestFixtureSeeder)->run();
            $this->fail('Seeder seharusnya menolak database yang bukan khusus retest.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('database', $exception->getMessage());
        }

        Storage::disk('azure')->assertMissing(RetestFixtureSeeder::OWNERSHIP_DOCUMENT_PATH);
    }
}
