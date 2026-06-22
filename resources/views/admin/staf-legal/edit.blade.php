<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Staf Legal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.staf-legal.update', $stafLegal) }}" class="p-6 space-y-6">
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
                        <select id="status_akun" name="status_akun" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="aktif" @selected(old('status_akun', $stafLegal->status_akun) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status_akun', $stafLegal->status_akun) === 'nonaktif')>Nonaktif</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status_akun')" />
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.staf-legal.show', $stafLegal) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.staf-legal.password', $stafLegal) }}" class="p-6 space-y-6">
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
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <div>
                        <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Ubah Password') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
