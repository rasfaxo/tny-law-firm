<?php

namespace Tests\Feature;

use App\Models\KategoriPerkara;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_only_creates_required_production_baseline(): void
    {
        config()->set('app.admin.name', 'Admin Rehearsal');
        config()->set('app.admin.email', 'admin-seeder@example.invalid');
        config()->set('app.admin.default_password', 'Aman-Untuk-Test-123!');

        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'admin-seeder@example.invalid',
            'role' => 'admin',
            'status_akun' => 'aktif',
        ]);
        $this->assertSame(
            ['Keluarga', 'Ketenagakerjaan', 'Perdata', 'Pidana'],
            KategoriPerkara::query()->orderBy('nama_kategori')->pluck('nama_kategori')->all(),
        );
    }

    public function test_admin_seeder_is_idempotent_and_does_not_reset_existing_password(): void
    {
        config()->set('app.admin.name', 'Admin Rehearsal');
        config()->set('app.admin.email', 'admin-seeder@example.invalid');
        config()->set('app.admin.default_password', 'Kata-Sandi-Pertama-123!');

        (new AdminSeeder)->run();

        $originalPassword = User::query()
            ->where('email', 'admin-seeder@example.invalid')
            ->value('password');

        config()->set('app.admin.default_password', 'Kata-Sandi-Berbeda-456!');
        (new AdminSeeder)->run();

        $this->assertDatabaseCount('users', 1);
        $this->assertSame(
            $originalPassword,
            User::query()->where('email', 'admin-seeder@example.invalid')->value('password'),
        );
    }

    public function test_existing_admin_can_be_seeded_without_bootstrap_password(): void
    {
        $admin = User::factory()->admin()->create([
            'email' => 'admin-seeder@example.invalid',
        ]);

        config()->set('app.admin.name', 'Admin Rehearsal');
        config()->set('app.admin.email', $admin->email);
        config()->set('app.admin.default_password');
        $this->app->detectEnvironment(fn (): string => 'production');

        (new AdminSeeder)->run();

        $this->assertDatabaseCount('users', 1);
        $this->assertSame($admin->password, $admin->fresh()->password);
    }

    public function test_new_production_admin_requires_bootstrap_password(): void
    {
        config()->set('app.admin.name', 'Admin Rehearsal');
        config()->set('app.admin.email', 'admin-baru@example.test');
        config()->set('app.admin.default_password');
        $this->app->detectEnvironment(fn (): string => 'production');

        $this->expectException(\RuntimeException::class);

        (new AdminSeeder)->run();
    }
}
