<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Admin TNY Law Firm',
                'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'password')),
                'role' => 'admin',
                'no_telepon' => null,
                'status_akun' => 'aktif',
            ]
        );
    }
}
