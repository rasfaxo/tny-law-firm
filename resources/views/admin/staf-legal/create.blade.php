<x-app-layout title="Tambah Akun Staf Legal" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Staf Legal', 'url' => route('admin.staf-legal.index')], ['label' => 'Tambah']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <x-secondary-button href="{{ route('admin.staf-legal.index') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>
        </div>

    <div class="max-w-2xl mx-auto">
        <x-card>
            <form method="POST" action="{{ route('admin.staf-legal.store') }}" class="space-y-6">
                @csrf

                <!-- Nama -->
                <div>
                    <x-input-label for="nama" value="Nama Lengkap" class="mb-2" />
                    <x-text-input id="nama" name="nama" type="text" class="w-full" value="{{ old('nama') }}" required autofocus placeholder="Masukkan nama lengkap staf legal" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                </div>

                <!-- Email -->
                <div>
                    <x-input-label for="email" value="Alamat Email" class="mb-2" />
                    <x-text-input id="email" name="email" type="email" class="w-full" value="{{ old('email') }}" required placeholder="Contoh: staf.legal@tny.co.id" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- No. Telepon -->
                <div>
                    <x-input-label for="no_telepon" value="Nomor Telepon" class="mb-2" />
                    <x-text-input id="no_telepon" name="no_telepon" type="text" class="w-full" value="{{ old('no_telepon') }}" placeholder="Masukkan nomor telepon aktif" />
                    <x-input-error class="mt-2" :messages="$errors->get('no_telepon')" />
                </div>

                <!-- Status Akun -->
                <div>
                    <x-input-label for="status_akun" value="Status Akun" class="mb-2" />
                    <x-select id="status_akun" name="status_akun" required>
                        <option value="aktif" @selected(old('status_akun', 'aktif') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(old('status_akun') === 'nonaktif')>Nonaktif</option>
                    </x-select>
                    <x-input-error class="mt-2" :messages="$errors->get('status_akun')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Password -->
                    <div>
                        <x-input-label for="password" value="Password" class="mb-2" />
                        <x-password-input id="password" name="password" class="w-full" required placeholder="Minimal 8 karakter" />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <x-input-label for="password_confirmation" value="Konfirmasi Password" class="mb-2" />
                        <x-password-input id="password_confirmation" name="password_confirmation" class="w-full" required placeholder="Ulangi password" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <x-secondary-button href="{{ route('admin.staf-legal.index') }}" tag="a">
                        {{ __('Batal') }}
                    </x-secondary-button>
                    <x-primary-button class="gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        <span>{{ __('Simpan') }}</span>
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
