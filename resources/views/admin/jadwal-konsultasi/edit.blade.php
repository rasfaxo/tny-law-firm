<x-app-layout title="Edit Jadwal Konsultasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Slot Jadwal', 'url' => route('admin.jadwal-konsultasi.index')], ['label' => 'Edit']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <x-secondary-button href="{{ route('admin.jadwal-konsultasi.index') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>
        </div>

    <div class="max-w-2xl mx-auto">
        <x-card>
            <form method="POST" action="{{ route('admin.jadwal-konsultasi.update', $jadwalKonsultasi) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="tanggal" :value="__('Tanggal')" />
                    <x-text-input id="tanggal" name="tanggal" type="date" :value="old('tanggal', $jadwalKonsultasi->tanggal?->format('Y-m-d'))" required autofocus class="w-full" />
                    @if($errors->has('tanggal'))
                        <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('tanggal') }}</div>
                    @endif
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <x-input-label for="waktu_mulai" :value="__('Waktu Mulai')" />
                        <x-text-input id="waktu_mulai" name="waktu_mulai" type="time" :value="old('waktu_mulai', substr((string) $jadwalKonsultasi->waktu_mulai, 0, 5))" required class="w-full" />
                        @if($errors->has('waktu_mulai'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('waktu_mulai') }}</div>
                        @endif
                    </div>

                    <div>
                        <x-input-label for="waktu_selesai" :value="__('Waktu Selesai')" />
                        <x-text-input id="waktu_selesai" name="waktu_selesai" type="time" :value="old('waktu_selesai', substr((string) $jadwalKonsultasi->waktu_selesai, 0, 5))" required class="w-full" />
                        @if($errors->has('waktu_selesai'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('waktu_selesai') }}</div>
                        @endif
                    </div>
                </div>

                <div>
                    <x-input-label for="status_slot" :value="__('Status Slot')" />
                    <x-select id="status_slot" name="status_slot" required class="mt-1">
                        <option value="tersedia" @selected(old('status_slot', $jadwalKonsultasi->status_slot) === 'tersedia')>Tersedia</option>
                        <option value="tidak_aktif" @selected(old('status_slot', $jadwalKonsultasi->status_slot) === 'tidak_aktif')>Tidak Aktif</option>
                    </x-select>
                    @if($errors->has('status_slot'))
                        <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('status_slot') }}</div>
                    @endif
                </div>

                <x-alert-banner type="warning">
                    {{ __('Slot dengan status terisi tidak dapat diedit. Jika status dibuat tersedia, sistem akan mengecek bentrok dengan slot tersedia atau terisi lainnya.') }}
                </x-alert-banner>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <x-secondary-button href="{{ route('admin.jadwal-konsultasi.index') }}" tag="a">
                        {{ __('Batal') }}
                    </x-secondary-button>
                    <x-primary-button type="submit">
                        {{ __('Simpan Perubahan') }}
                    </x-primary-button>
                </div>
            </form>
        </x-card>
    </div>
</x-app-layout>
