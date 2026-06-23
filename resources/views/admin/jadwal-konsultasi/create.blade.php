<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tambah Jadwal Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.jadwal-konsultasi.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="tanggal" :value="__('Tanggal')" />
                        <x-text-input id="tanggal" name="tanggal" type="date" class="mt-1 block w-full" :value="old('tanggal')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('tanggal')" />
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div>
                            <x-input-label for="waktu_mulai" :value="__('Waktu Mulai')" />
                            <x-text-input id="waktu_mulai" name="waktu_mulai" type="time" class="mt-1 block w-full" :value="old('waktu_mulai')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('waktu_mulai')" />
                        </div>

                        <div>
                            <x-input-label for="waktu_selesai" :value="__('Waktu Selesai')" />
                            <x-text-input id="waktu_selesai" name="waktu_selesai" type="time" class="mt-1 block w-full" :value="old('waktu_selesai')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('waktu_selesai')" />
                        </div>
                    </div>

                    <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-800">
                        {{ __('Status slot baru otomatis tersedia. Sistem akan menolak jadwal yang bentrok dengan slot tersedia atau terisi pada tanggal yang sama.') }}
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('admin.jadwal-konsultasi.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
