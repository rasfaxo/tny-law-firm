<x-app-layout title="Edit Staf Legal" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Staf Legal', 'url' => route('admin.staf-legal.index')], ['label' => 'Edit']]">

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
            @if (session('success'))
                <x-alert-banner type="success" class="mb-6">
                    {{ session('success') }}
                </x-alert-banner>
            @endif

            <x-card>
                <form method="POST" action="{{ route('admin.staf-legal.update', $stafLegal) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nama" :value="__('Nama')" />
                        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $stafLegal->nama)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $stafLegal->email)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <x-input-label for="no_telepon" :value="__('No. Telepon')" />
                        <x-text-input id="no_telepon" name="no_telepon" type="text" class="mt-1 block w-full" :value="old('no_telepon', $stafLegal->no_telepon)" />
                        <x-input-error class="mt-2" :messages="$errors->get('no_telepon')" />
                    </div>

                    <div>
                        <x-input-label for="status_akun" :value="__('Status Akun')" />
                        <x-select id="status_akun" name="status_akun" class="mt-1" required>
                            <option value="aktif" @selected(old('status_akun', $stafLegal->status_akun) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status_akun', $stafLegal->status_akun) === 'nonaktif')>Nonaktif</option>
                        </x-select>
                        <x-input-error class="mt-2" :messages="$errors->get('status_akun')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-secondary-button href="{{ route('admin.staf-legal.show', $stafLegal) }}" tag="a">
                            {{ __('Batal') }}
                        </x-secondary-button>
                        <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                    </div>
                </form>
            </x-card>

            <x-card class="mt-6">
                <form method="POST" action="{{ route('admin.staf-legal.password', $stafLegal) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Ubah Password') }}</h3>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ __('Gunakan form ini hanya jika Admin perlu mengganti password Staf Legal secara manual.') }}
                        </p>
                    </div>

                    <div>
                        <x-input-label for="password" :value="__('Password Baru')" />
                        <x-password-input id="password" name="password" class="mt-1 block w-full" required />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                        <x-password-input id="password_confirmation" name="password_confirmation" class="mt-1 block w-full" required />
                    </div>

                    <div class="flex justify-end pt-2">
                        <x-primary-button>{{ __('Ubah Password') }}</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
