<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Jadwal Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.jadwal-konsultasi.update', $jadwalKonsultasi) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="tanggal" :value="__('Tanggal')" />
                        <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" :value="old('tanggal', $jadwalKonsultasi->tanggal?->format('Y-m-d'))" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="waktu_mulai" :value="__('Waktu Mulai')" />
                            <x-text-input id="waktu_mulai" name="waktu_mulai" type="time" class="mt-1 block w-full" :value="old('waktu_mulai', substr((string) $jadwalKonsultasi->waktu_mulai, 0, 5))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('waktu_mulai')" />
                        </div>

                        <div>
                            <x-input-label for="waktu_selesai" :value="__('Waktu Selesai')" />
                            <x-text-input id="waktu_selesai" name="waktu_selesai" type="time" class="mt-1 block w-full" :value="old('waktu_selesai', substr((string) $jadwalKonsultasi->waktu_selesai, 0, 5))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('waktu_selesai')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="status_slot" :value="__('Status Slot')" />
                        <select id="status_slot" name="status_slot" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="tersedia" @selected(old('status_slot', $jadwalKonsultasi->status_slot) === 'tersedia')>{{ __('Tersedia') }}</option>
                            <option value="tidak_aktif" @selected(old('status_slot', $jadwalKonsultasi->status_slot) === 'tidak_aktif')>{{ __('Tidak Aktif') }}</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status_slot')" />
                    </div>

                    <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                        {{ __('Slot dengan status terisi tidak dapat diedit. Jika status dibuat tersedia, sistem akan mengecek bentrok dengan slot tersedia atau terisi lainnya.') }}
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.jadwal-konsultasi.show', $jadwalKonsultasi) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>{{ __('Simpan Perubahan') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
