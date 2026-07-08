<x-app-layout title="Tambah Jadwal Konsultasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Slot Jadwal', 'url' => route('admin.jadwal-konsultasi.index')], ['label' => 'Tambah']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <a href="{{ route('admin.jadwal-konsultasi.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </a>
        </div>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm">
            <form method="POST" action="{{ route('admin.jadwal-konsultasi.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="tanggal" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal</label>
                    <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal') }}" required autofocus
                        class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                    @if($errors->has('tanggal'))
                        <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('tanggal') }}</div>
                    @endif
                </div>

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="waktu_mulai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Waktu Mulai</label>
                        <input id="waktu_mulai" name="waktu_mulai" type="time" value="{{ old('waktu_mulai') }}" required
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                        @if($errors->has('waktu_mulai'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('waktu_mulai') }}</div>
                        @endif
                    </div>

                    <div>
                        <label for="waktu_selesai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Waktu Selesai</label>
                        <input id="waktu_selesai" name="waktu_selesai" type="time" value="{{ old('waktu_selesai') }}" required
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                        @if($errors->has('waktu_selesai'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('waktu_selesai') }}</div>
                        @endif
                    </div>
                </div>

                <div class="bg-blue-50 border border-blue-200 text-[#1E3A8A] rounded-xl p-4 text-xs font-medium leading-relaxed">
                    {{ __('Status slot baru otomatis tersedia. Sistem akan menolak jadwal yang bentrok dengan slot tersedia atau terisi pada tanggal yang sama.') }}
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <a href="{{ route('admin.jadwal-konsultasi.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-sm">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest">
                        {{ __('Simpan Jadwal') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
