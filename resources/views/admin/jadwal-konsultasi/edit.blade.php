<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                    <span>Admin</span>
                    <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span>Slot Jadwal</span>
                    <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-600">Edit Jadwal</span>
                </div>
                <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
                    {{ __('Edit Jadwal Konsultasi') }}
                </h2>
            </div>

            <a href="{{ route('admin.jadwal-konsultasi.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-sm gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm">
            <form method="POST" action="{{ route('admin.jadwal-konsultasi.update', $jadwalKonsultasi) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="tanggal" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                    <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $jadwalKonsultasi->tanggal?->format('Y-m-d')) }}" required autofocus
                        class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                    @if($errors->has('tanggal'))
                        <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('tanggal') }}</div>
                    @endif
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="waktu_mulai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Waktu Mulai</label>
                        <input id="waktu_mulai" name="waktu_mulai" type="time" value="{{ old('waktu_mulai', substr((string) $jadwalKonsultasi->waktu_mulai, 0, 5)) }}" required
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                        @if($errors->has('waktu_mulai'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('waktu_mulai') }}</div>
                        @endif
                    </div>

                    <div>
                        <label for="waktu_selesai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Waktu Selesai</label>
                        <input id="waktu_selesai" name="waktu_selesai" type="time" value="{{ old('waktu_selesai', substr((string) $jadwalKonsultasi->waktu_selesai, 0, 5)) }}" required
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                        @if($errors->has('waktu_selesai'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('waktu_selesai') }}</div>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="status_slot" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Status Slot</label>
                    <select id="status_slot" name="status_slot" required
                        class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                        <option value="tersedia" @selected(old('status_slot', $jadwalKonsultasi->status_slot) === 'tersedia')>Tersedia</option>
                        <option value="tidak_aktif" @selected(old('status_slot', $jadwalKonsultasi->status_slot) === 'tidak_aktif')>Tidak Aktif</option>
                    </select>
                    @if($errors->has('status_slot'))
                        <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('status_slot') }}</div>
                    @endif
                </div>

                <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-4 text-xs font-medium leading-relaxed">
                    {{ __('Slot dengan status terisi tidak dapat diedit. Jika status dibuat tersedia, sistem akan mengecek bentrok dengan slot tersedia atau terisi lainnya.') }}
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <a href="{{ route('admin.jadwal-konsultasi.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-sm">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest">
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
