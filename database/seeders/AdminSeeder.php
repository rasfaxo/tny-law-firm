<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $email = $this->requiredConfig('app.admin.email', 'ADMIN_EMAIL', 'admin@example.com');
        $name = $this->requiredConfig('app.admin.name', 'ADMIN_NAME', 'Admin TNY Law Firm');

        if (User::query()->where('email', $email)->exists()) {
            return;
        }

        User::query()->create([
            'nama' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($this->resolveDefaultPassword()),
            'role' => 'admin',
            'no_telepon' => null,
            'status_akun' => 'aktif',
        ]);
    }

    private function requiredConfig(string $key, string $environmentKey, string $localFallback): string
    {
        $value = config($key);

        if (is_string($value) && $value !== '') {
            return $value;
        }

        if (app()->environment('production')) {
            throw new RuntimeException("{$environmentKey} belum didefinisikan untuk bootstrap Admin.");
        }

        return $localFallback;
    }

    /**
     * Resolve password default akun admin.
     *
     * Baca via config() agar tetap berfungsi setelah `php artisan config:cache`
     * (env() langsung dari kode runtime akan return null setelah cache aktif).
     *
     * - Di production: lempar exception bila kosong agar tidak diam-diam
     *   jatuh ke password lemah.
     * - Di local/testing: izinkan fallback 'password' untuk kenyamanan dev.
     */
    private function resolveDefaultPassword(): string
    {
        $password = config('app.admin.default_password');

        if ($password !== null && $password !== '') {
            return $password;
        }

        if (app()->environment('production')) {
            throw new RuntimeException(
                'ADMIN_DEFAULT_PASSWORD belum didefinisikan. '.
                'Tambahkan key ADMIN_DEFAULT_PASSWORD pada file .env '.
                'sebelum menjalankan seeder di production.'
            );
        }

        return 'password';
    }
}
