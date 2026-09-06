<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ProductionReadinessCheck extends Command
{
    protected $signature = 'app:production-readiness
        {--phase=runtime : Fase pemeriksaan: bootstrap atau runtime}
        {--external : Verifikasi koneksi read-only ke Resend dan Azure}
        {--storage-roundtrip : Buat, baca, dan hapus object uji pada prefix release-gate Azure}';

    protected $description = 'Validate production configuration and hosting capabilities without exposing secret values';

    /** @var list<array{0: string, 1: bool, 2: string}> */
    private array $checks = [];

    private bool $databaseAvailable = false;

    public function handle(): int
    {
        $phase = strtolower((string) $this->option('phase'));

        if (! in_array($phase, ['bootstrap', 'runtime'], true)) {
            $this->components->error('Nilai --phase harus bootstrap atau runtime.');

            return self::INVALID;
        }

        $this->runConfigurationChecks();
        $this->runPlatformChecks();
        $this->runDatabaseChecks($phase);
        $this->runPhaseChecks($phase);

        if ($this->option('external')) {
            $this->runExternalChecks();
        }

        if ($this->option('storage-roundtrip')) {
            $this->runStorageRoundTrip();
        }

        $failed = false;

        foreach ($this->checks as [$label, $passed, $detail]) {
            if ($passed) {
                $this->components->info($label);
            } else {
                $this->components->error($label.($detail ? ": {$detail}" : ''));
                $failed = true;
            }
        }

        $this->newLine();

        if ($failed) {
            $this->warn("Production readiness fase {$phase} belum terpenuhi. Nilai secret sengaja tidak ditampilkan.");

            return self::FAILURE;
        }

        $this->info("Seluruh pemeriksaan production readiness fase {$phase} lulus.");

        return self::SUCCESS;
    }

    private function runConfigurationChecks(): void
    {
        $this->check('APP_DEBUG dinonaktifkan', ! config('app.debug'));
        $this->check('APP_URL menggunakan HTTPS', str_starts_with((string) config('app.url'), 'https://'));
        $this->check('Timezone Asia/Jakarta', config('app.timezone') === 'Asia/Jakarta');
        $this->check('Locale Indonesia', config('app.locale') === 'id');
        $this->check('Session database', config('session.driver') === 'database');
        $this->check('Queue database', config('queue.default') === 'database');
        $this->check('Mailer Resend', config('mail.default') === 'resend');
        $this->check('Alamat pengirim tersedia', $this->validEmail(config('mail.from.address')));
        $this->check('Reply-To firma tersedia', $this->validEmail(config('mail.reply_to.address')));
        $this->check('Resend API key tersedia', filled(config('services.resend.key')));
        $this->check('Document disk Azure', config('filesystems.document_disk') === 'azure');
        $this->check('Azure connection tersedia', filled(config('filesystems.disks.azure.connection_string')));
        $this->check('Azure container tersedia', filled(config('filesystems.disks.azure.container')));
        $this->check('Trusted hosts eksplisit', $this->hasTrustedHosts());
        $this->check(
            'Trusted proxy mode eksplisit',
            in_array(config('security.trusted_proxy_mode'), ['none', 'list'], true),
            'gunakan TRUSTED_PROXIES=none bila tidak ada reverse proxy',
        );
        $this->check('Logo resmi tersedia', is_file(public_path('brand/logo.svg')));
        $this->check('Logo email tersedia', is_file(public_path('brand/logo-email.png')));
    }

    private function runPlatformChecks(): void
    {
        $this->check('PHP minimal 8.4', version_compare(PHP_VERSION, '8.4.0', '>='), PHP_VERSION);

        $extensions = ['curl', 'dom', 'fileinfo', 'intl', 'mbstring', 'openssl', 'pdo', 'xml', 'zip'];
        $driver = config('database.default');

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            $extensions[] = 'pdo_mysql';
        } elseif ($driver === 'sqlite') {
            $extensions[] = 'pdo_sqlite';
        }

        foreach (array_unique($extensions) as $extension) {
            $this->check("Extension PHP {$extension}", extension_loaded($extension));
        }

        $this->check('CLI upload_max_filesize minimal 8M', $this->iniBytes('upload_max_filesize') >= 8 * 1024 * 1024);
        $this->check('CLI post_max_size minimal 32M', $this->iniBytes('post_max_size') >= 32 * 1024 * 1024);
        $this->check('CLI memory_limit minimal 256M', $this->iniUnlimitedOrAtLeast('memory_limit', 256 * 1024 * 1024));
        $this->check('CLI max_execution_time minimal 120 detik atau unlimited', $this->iniSecondsAllowUnlimited('max_execution_time', 120));
        $this->check('CLI max_input_time minimal 120 detik atau unlimited', $this->iniSecondsAllowUnlimited('max_input_time', 120));

        $this->check('Direktori storage writable', is_dir(storage_path()) && is_writable(storage_path()));
        $this->check('Direktori bootstrap/cache writable', is_dir(base_path('bootstrap/cache')) && is_writable(base_path('bootstrap/cache')));
    }

    private function runDatabaseChecks(string $phase): void
    {
        try {
            DB::connection()->getPdo();
            $this->databaseAvailable = true;
            $this->check('Koneksi database tersedia', true);
        } catch (Throwable) {
            $this->check('Koneksi database tersedia', false, 'periksa konfigurasi dan hak user database');

            return;
        }

        if ($phase !== 'runtime') {
            return;
        }

        foreach (['users', 'sessions', 'jobs', 'failed_jobs', 'privacy_consents', 'audit_logs'] as $table) {
            $this->check("Tabel runtime {$table} tersedia", Schema::hasTable($table));
        }
    }

    private function runPhaseChecks(string $phase): void
    {
        $this->check('Nama Admin bootstrap tersedia', filled(config('app.admin.name')));
        $this->check('Email Admin bootstrap tersedia', $this->validEmail(config('app.admin.email')));

        if ($phase === 'bootstrap') {
            $this->check('Password Admin bootstrap tersedia', filled(config('app.admin.default_password')));

            return;
        }

        $this->check('Password Admin bootstrap sudah dihapus', blank(config('app.admin.default_password')));
        $this->check('Kebijakan privasi siap', config('privacy.ready') === true);
        $this->check('Versi kebijakan privasi tersedia', filled(config('privacy.policy_version')));
        $this->check('Tanggal kebijakan privasi tersedia', filled(config('privacy.effective_date')));

        if ($this->databaseAvailable && Schema::hasTable('users') && $this->validEmail(config('app.admin.email'))) {
            $adminExists = User::query()
                ->where('email', config('app.admin.email'))
                ->where('role', 'admin')
                ->where('status_akun', 'aktif')
                ->exists();
            $this->check('Admin runtime aktif tersedia', $adminExists);
        } else {
            $this->check('Admin runtime aktif tersedia', false);
        }
    }

    private function runExternalChecks(): void
    {
        try {
            $response = Http::withToken((string) config('services.resend.key'))
                ->acceptJson()
                ->timeout(10)
                ->get('https://api.resend.com/domains');

            $host = parse_url((string) config('app.url'), PHP_URL_HOST);
            $verified = $response->successful()
                && collect($response->json('data', []))->contains(
                    fn (array $domain): bool => ($domain['name'] ?? null) === $host
                        && ($domain['status'] ?? null) === 'verified',
                );
            $this->check('Domain Resend terverifikasi', $verified, 'periksa API key, SPF, dan DKIM');
        } catch (Throwable) {
            $this->check('Domain Resend terverifikasi', false, 'koneksi HTTPS atau respons Resend gagal');
        }

        try {
            Storage::disk('azure')->files('');
            $this->check('Azure Blob dapat diakses secara read-only', true);
        } catch (Throwable) {
            $this->check('Azure Blob dapat diakses secara read-only', false, 'periksa koneksi, container, SAS, dan outbound HTTPS');
        }
    }

    private function runStorageRoundTrip(): void
    {
        $prefix = trim((string) config('filesystems.disks.azure.prefix'), '/');

        if (config('filesystems.document_disk') !== 'azure' || ! str_contains(strtolower($prefix), 'release-gate')) {
            $this->check(
                'Azure storage round-trip',
                false,
                'AZURE_STORAGE_PREFIX wajib non-kosong dan memuat release-gate',
            );

            return;
        }

        $path = 'readiness/'.Str::uuid().'.txt';
        $content = Str::random(64);

        try {
            $disk = Storage::disk('azure');
            $written = $disk->put($path, $content);
            $matches = $written && $disk->exists($path) && hash_equals($content, (string) $disk->get($path));
            $deleted = $disk->delete($path) && ! $disk->exists($path);
            $this->check('Azure storage round-trip', $matches && $deleted);
        } catch (Throwable) {
            $this->check('Azure storage round-trip', false, 'operasi put/read/delete gagal');
        } finally {
            try {
                if (isset($disk) && $disk->exists($path)) {
                    $disk->delete($path);
                }
            } catch (Throwable) {
                // Kegagalan cleanup tetap tercatat sebagai kegagalan round-trip di atas.
            }
        }
    }

    private function hasTrustedHosts(): bool
    {
        $hosts = config('security.trusted_hosts', []);

        return is_array($hosts)
            && $hosts !== []
            && collect($hosts)->every(
                fn ($host): bool => is_string($host) && ! str_contains($host, '.*'),
            );
    }

    private function validEmail(mixed $value): bool
    {
        return is_string($value)
            && filter_var($value, FILTER_VALIDATE_EMAIL) !== false
            && ! str_ends_with($value, '@example.invalid');
    }

    private function iniUnlimitedOrAtLeast(string $key, int $minimumBytes): bool
    {
        $value = trim((string) ini_get($key));

        return $value === '-1' || $this->iniBytes($key) >= $minimumBytes;
    }

    private function iniSecondsAllowUnlimited(string $key, int $minimumSeconds): bool
    {
        $value = (int) ini_get($key);

        return $value <= 0 || $value >= $minimumSeconds;
    }

    private function iniBytes(string $key): int
    {
        $value = strtolower(trim((string) ini_get($key)));

        if ($value === '' || $value === '-1') {
            return $value === '-1' ? PHP_INT_MAX : 0;
        }

        $number = (float) $value;

        return match (substr($value, -1)) {
            'g' => (int) ($number * 1024 * 1024 * 1024),
            'm' => (int) ($number * 1024 * 1024),
            'k' => (int) ($number * 1024),
            default => (int) $number,
        };
    }

    private function check(string $label, bool $passed, ?string $detail = null): void
    {
        $this->checks[] = [$label, $passed, $detail ?? ''];
    }
}
