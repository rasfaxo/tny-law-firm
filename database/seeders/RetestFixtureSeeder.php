<?php

namespace Database\Seeders;

use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\KategoriPerkara;
use App\Models\PraPendaftaranPerkara;
use App\Models\PrivacyConsent;
use App\Models\ProfilKlien;
use App\Models\RiwayatStatus;
use App\Models\User;
use App\Models\VerifikasiBerkas;
use Illuminate\Database\Seeder;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class RetestFixtureSeeder extends Seeder
{
    public const ADMIN_EMAIL = 'admin.retest.v100@example.test';

    public const CLIENT_A_EMAIL = 'client-a.retest.v100@example.test';

    public const CLIENT_B_EMAIL = 'client-b.retest.v100@example.test';

    public const LEGAL_EMAIL = 'legal.retest.v100@example.test';

    public const CATEGORY_NAME = 'Retest Azure v1.0.0';

    public const OWNERSHIP_CASE_TITLE = 'Retest Ownership Azure v1.0.0';

    public const REPAIR_CASE_TITLE = 'Retest Perbaikan Azure v1.0.0';

    public const OWNERSHIP_DOCUMENT_PATH = 'fixtures/v1.0.0/ownership-document.pdf';

    public const REPAIR_DOCUMENT_PATH = 'fixtures/v1.0.0/repair-original-document.pdf';

    private const EXPECTED_APP_URL = 'https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net';

    private const EXPECTED_CONFIRMATION = 'SEED-AZURE-V100-RETEST-ONLY';

    private const EXPECTED_CONTAINER = 'documents';

    private const EXPECTED_PREFIX = 'retest/v1.0.0/tnypartners';

    private const EXPECTED_BLOB_HOST = 'tnylawfirmstorage.blob.core.windows.net';

    private const PDF_CONTENT = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF\n";

    /**
     * Create anonymous, repeatable fixtures for the isolated Azure retest.
     */
    public function run(): void
    {
        if (! config('retest.fixtures.enabled', false)) {
            return;
        }

        $password = $this->validateSafetyGate();
        $disk = Storage::disk('azure');
        $createdPaths = [];

        try {
            foreach ([self::OWNERSHIP_DOCUMENT_PATH, self::REPAIR_DOCUMENT_PATH] as $path) {
                if ($disk->exists($path)) {
                    continue;
                }

                if (! $disk->put($path, self::PDF_CONTENT)) {
                    throw new RuntimeException("Fixture blob gagal ditulis: {$path}");
                }

                $createdPaths[] = $path;
            }

            $inventory = DB::transaction(fn (): array => $this->seedDatabase($password));
        } catch (Throwable $exception) {
            $this->deleteNewBlobs($disk, $createdPaths);

            throw $exception;
        }

        $this->command?->info('Retest fixture Azure v1.0.0 berhasil dibuat.');
        $this->command?->line('RETEST_CATEGORY_ID='.$inventory['category_id']);
        $this->command?->line('SECURITY_OTHER_CASE_ID='.$inventory['ownership_case_id']);
        $this->command?->line('SECURITY_REPAIR_CASE_ID='.$inventory['repair_case_id']);
        $this->command?->line('SECURITY_REPAIR_OLD_DOCUMENT_ID='.$inventory['repair_document_id']);
        $this->command?->line('SECURITY_REPAIR_NOTE_ID='.$inventory['repair_note_id']);
    }

    private function validateSafetyGate(): string
    {
        $appUrl = rtrim((string) config('app.url'), '/');

        if ($appUrl !== self::EXPECTED_APP_URL) {
            throw new RuntimeException('RetestFixtureSeeder ditolak: APP_URL bukan hostname Azure staging yang diizinkan.');
        }

        if (config('retest.fixtures.confirmation') !== self::EXPECTED_CONFIRMATION) {
            throw new RuntimeException('RetestFixtureSeeder ditolak: RETEST_FIXTURE_CONFIRMATION tidak sesuai.');
        }

        if (config('filesystems.document_disk') !== 'azure') {
            throw new RuntimeException('RetestFixtureSeeder ditolak: DOCUMENT_DISK wajib azure.');
        }

        if (config('filesystems.disks.azure.container') !== self::EXPECTED_CONTAINER) {
            throw new RuntimeException('RetestFixtureSeeder ditolak: container Azure bukan container retest v1.0.0.');
        }

        if (trim((string) config('filesystems.disks.azure.prefix'), '/') !== self::EXPECTED_PREFIX) {
            throw new RuntimeException('RetestFixtureSeeder ditolak: prefix Azure bukan prefix retest v1.0.0.');
        }

        $this->validateAzureConnectionString();

        if (app()->environment('production')) {
            $databaseConnection = (string) config('database.default');
            $databaseName = (string) config("database.connections.{$databaseConnection}.database");

            if ($databaseConnection !== 'mysql' || ! str_contains(strtolower($databaseName), 'retest')) {
                throw new RuntimeException('RetestFixtureSeeder ditolak: database wajib MySQL khusus retest.');
            }
        }

        if (! config('privacy.ready') || ! is_string(config('privacy.policy_version')) || config('privacy.policy_version') === '') {
            throw new RuntimeException('RetestFixtureSeeder ditolak: kebijakan privasi staging belum siap.');
        }

        $password = config('retest.fixtures.password');

        if (! is_string($password) || mb_strlen($password) < 16) {
            throw new RuntimeException('RETEST_FIXTURE_PASSWORD wajib berisi minimal 16 karakter.');
        }

        return $password;
    }

    private function validateAzureConnectionString(): void
    {
        $connectionString = config('filesystems.disks.azure.connection_string');

        if (! is_string($connectionString) || $connectionString === '') {
            throw new RuntimeException('RetestFixtureSeeder ditolak: connection string Azure tidak tersedia.');
        }

        $segments = [];

        foreach (explode(';', $connectionString) as $segment) {
            if ($segment === '') {
                continue;
            }

            if (! str_contains($segment, '=')) {
                throw new RuntimeException('RetestFixtureSeeder ditolak: format connection string Azure tidak valid.');
            }

            [$key, $value] = explode('=', $segment, 2);
            $segments[$key] = $value;
        }

        $endpoint = $segments['BlobEndpoint'] ?? null;
        $sas = $segments['SharedAccessSignature'] ?? null;
        $endpointHost = is_string($endpoint) ? parse_url($endpoint, PHP_URL_HOST) : null;

        if ($endpointHost !== self::EXPECTED_BLOB_HOST || ! is_string($sas) || $sas === '') {
            throw new RuntimeException('RetestFixtureSeeder ditolak: format endpoint atau SAS Azure tidak valid.');
        }

        parse_str(ltrim($sas, '?'), $sasParameters);
        $permissions = is_string($sasParameters['sp'] ?? null) ? $sasParameters['sp'] : '';

        foreach (['c', 'r', 'w', 'd', 'l'] as $requiredPermission) {
            if (! str_contains($permissions, $requiredPermission)) {
                throw new RuntimeException('RetestFixtureSeeder ditolak: SAS Azure tidak memiliki izin create/read/write/delete/list.');
            }
        }
    }

    /**
     * @return array{category_id: int, ownership_case_id: int, repair_case_id: int, repair_document_id: int, repair_note_id: int}
     */
    private function seedDatabase(string $password): array
    {
        $users = [
            'admin' => $this->upsertUser('Admin Retest v1.0.0', self::ADMIN_EMAIL, 'admin', $password),
            'legal' => $this->upsertUser('Staf Legal Retest v1.0.0', self::LEGAL_EMAIL, 'staf_legal', $password),
            'client_a' => $this->upsertUser('Klien A Retest v1.0.0', self::CLIENT_A_EMAIL, 'klien', $password),
            'client_b' => $this->upsertUser('Klien B Retest v1.0.0', self::CLIENT_B_EMAIL, 'klien', $password),
        ];

        $this->seedClientProfile($users['client_a'], 'A');
        $this->seedClientProfile($users['client_b'], 'B');
        $this->seedPrivacyConsent($users['client_a']);
        $this->seedPrivacyConsent($users['client_b']);

        $category = KategoriPerkara::query()->firstOrCreate(
            ['nama_kategori' => self::CATEGORY_NAME],
            ['deskripsi' => 'Kategori anonim khusus pengujian ulang Azure App Service v1.0.0.'],
        );

        $ownershipCase = $this->firstOrCreateCase(
            $users['client_b'],
            $category,
            self::OWNERSHIP_CASE_TITLE,
            'menunggu_verifikasi',
        );
        $ownershipDocument = $this->firstOrCreateDocument(
            $ownershipCase,
            self::OWNERSHIP_DOCUMENT_PATH,
            'terkirim',
        );
        $this->firstOrCreateHistory(
            $ownershipCase,
            $users['client_b'],
            'menunggu_verifikasi',
            'Fixture perkara ownership dibuat untuk retest Azure v1.0.0.',
        );

        $repairCase = $this->firstOrCreateCase(
            $users['client_a'],
            $category,
            self::REPAIR_CASE_TITLE,
            'berkas_tidak_lengkap',
        );
        $repairDocument = $this->firstOrCreateDocument(
            $repairCase,
            self::REPAIR_DOCUMENT_PATH,
            'perlu_perbaikan',
        );
        $verification = VerifikasiBerkas::query()->firstOrCreate(
            [
                'id_pendaftaran' => $repairCase->id_pendaftaran,
                'id_user' => $users['legal']->id_user,
                'status_verifikasi' => 'berkas_tidak_lengkap',
            ],
            [
                'tanggal_verifikasi' => now(),
                'catatan_umum' => 'Fixture retest: dokumen perlu diperbaiki.',
            ],
        );
        $repairNote = CatatanVerifikasi::query()->firstOrCreate(
            [
                'id_verifikasi' => $verification->id_verifikasi,
                'id_dokumen' => $repairDocument->id_dokumen,
                'status_perbaikan' => 'belum_diperbaiki',
            ],
            ['isi_catatan' => 'Fixture retest: unggah dokumen pengganti yang valid.'],
        );
        $this->firstOrCreateHistory(
            $repairCase,
            $users['client_a'],
            'menunggu_verifikasi',
            'Fixture perkara perbaikan dibuat untuk retest Azure v1.0.0.',
        );
        $this->firstOrCreateHistory(
            $repairCase,
            $users['legal'],
            'berkas_tidak_lengkap',
            'Fixture dokumen ditandai perlu perbaikan oleh Staf Legal.',
        );

        return [
            'category_id' => $category->id_kategori,
            'ownership_case_id' => $ownershipCase->id_pendaftaran,
            'repair_case_id' => $repairCase->id_pendaftaran,
            'repair_document_id' => $repairDocument->id_dokumen,
            'repair_note_id' => $repairNote->id_catatan,
        ];
    }

    private function upsertUser(string $name, string $email, string $role, string $password): User
    {
        $user = User::query()->where('email', $email)->first();

        if ($user && $user->role !== $role) {
            throw new RuntimeException("Fixture email {$email} sudah dipakai oleh role lain.");
        }

        if (! $user) {
            return User::query()->create([
                'nama' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => $password,
                'role' => $role,
                'no_telepon' => null,
                'status_akun' => 'aktif',
            ]);
        }

        $changes = [
            'nama' => $name,
            'status_akun' => 'aktif',
        ];

        if ($user->email_verified_at === null) {
            $changes['email_verified_at'] = now();
        }

        if (! Hash::check($password, $user->password)) {
            $changes['password'] = $password;
        }

        $user->update($changes);

        return $user->fresh();
    }

    private function seedClientProfile(User $client, string $label): void
    {
        ProfilKlien::query()->updateOrCreate(
            ['id_user' => $client->id_user],
            [
                'alamat' => "Alamat anonim fixture Klien {$label}",
                'jenis_kelamin' => null,
                'pekerjaan' => 'Data uji anonim',
                'no_identitas' => null,
            ],
        );
    }

    private function seedPrivacyConsent(User $client): void
    {
        PrivacyConsent::query()->firstOrCreate(
            [
                'id_user' => $client->id_user,
                'policy_version' => config('privacy.policy_version'),
            ],
            ['agreed_at' => now()],
        );
    }

    private function firstOrCreateCase(
        User $client,
        KategoriPerkara $category,
        string $title,
        string $status,
    ): PraPendaftaranPerkara {
        $case = PraPendaftaranPerkara::query()->firstOrCreate(
            [
                'id_user' => $client->id_user,
                'judul_perkara' => $title,
            ],
            [
                'id_kategori' => $category->id_kategori,
                'kronologi' => 'Kronologi anonim khusus fixture pengujian sistem.',
                'status_pengajuan' => $status,
                'tanggal_pengajuan' => now(),
            ],
        );

        if ($case->id_kategori !== $category->id_kategori || $case->status_pengajuan !== $status) {
            throw new RuntimeException("Fixture perkara {$title} sudah ada dengan state berbeda.");
        }

        return $case;
    }

    private function firstOrCreateDocument(
        PraPendaftaranPerkara $case,
        string $path,
        string $status,
    ): DokumenPerkara {
        $document = DokumenPerkara::query()->firstOrCreate(
            [
                'id_pendaftaran' => $case->id_pendaftaran,
                'file_path' => $path,
            ],
            [
                'nama_dokumen' => 'Dokumen fixture anonim',
                'jenis_dokumen' => 'bukti_pendukung',
                'status_dokumen' => $status,
            ],
        );

        if ($document->status_dokumen !== $status) {
            throw new RuntimeException("Fixture dokumen {$path} sudah ada dengan state berbeda.");
        }

        return $document;
    }

    private function firstOrCreateHistory(
        PraPendaftaranPerkara $case,
        User $actor,
        string $status,
        string $description,
    ): void {
        RiwayatStatus::query()->firstOrCreate([
            'id_pendaftaran' => $case->id_pendaftaran,
            'id_user' => $actor->id_user,
            'status' => $status,
            'keterangan' => $description,
        ]);
    }

    /** @param array<int, string> $paths */
    private function deleteNewBlobs(FilesystemAdapter $disk, array $paths): void
    {
        foreach ($paths as $path) {
            try {
                $disk->delete($path);
            } catch (Throwable) {
                // Pertahankan exception awal; cleanup hanya kompensasi best effort.
            }
        }
    }
}
