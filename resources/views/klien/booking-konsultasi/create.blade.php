<x-app-layout title="Pilih Jadwal Konsultasi" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Pengajuan', 'url' => route('klien.pra-pendaftaran.index')], ['label' => 'PP-' . str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT), 'url' => route('klien.pra-pendaftaran.show', $praPendaftaranPerkara)], ['label' => 'Pilih Jadwal']]">

    <!-- Hidden form for Date Filtering (GET) -->
    <form id="filter-form" method="GET" action="{{ route('klien.booking-konsultasi.create', $praPendaftaranPerkara) }}">
        <input type="hidden" name="tanggal" id="filter-tanggal-hidden" value="{{ request('tanggal') }}">
    </form>

    <div class="space-y-6" x-data="{ isSubmitting: false }">
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex gap-3 text-sm text-red-700 shadow-sm" x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); })">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex gap-3 text-sm text-red-700 shadow-sm">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif



        <!-- Card Info Pengajuan -->
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div class="space-y-4 max-w-2xl">
                <div>
                    <h3 class="font-extrabold text-navy-dark text-lg sm:text-xl leading-tight">
                        PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }} — {{ $praPendaftaranPerkara->judul_perkara }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Jadwal dapat dipilih karena berkas pengajuan telah dinyatakan lengkap. Pilih metode dan salah satu slot jadwal yang tersedia.
                    </p>
                </div>
                <div class="inline-flex">
                    <x-status-badge status="berkas_lengkap" />
                </div>
            </div>
            
            <div class="flex flex-row md:flex-col gap-6 md:gap-4 md:text-right border-t md:border-t-0 pt-4 md:pt-0 border-gray-100 shrink-0">
                <div>
                    <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Kategori</span>
                    <span class="block text-sm font-bold text-navy-dark mt-1">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Kode Pengajuan</span>
                    <span class="block text-sm font-bold text-accent-blue font-mono mt-1">PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
        </div>

        <!-- Main Form (Booking POST) -->
        <form method="POST" action="{{ route('klien.booking-konsultasi.store', $praPendaftaranPerkara) }}" @submit="isSubmitting = true">
            @csrf

            <!-- 2-Column Grid Layout matching Figma 73:2 -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                
                <!-- LEFT COLUMN: Pilih Slot Jadwal -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                    <!-- Section Title -->
                    <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                        <h3 class="font-bold text-navy-dark text-lg">Pilih Slot Jadwal</h3>
                        <p class="text-xs text-gray-500 mt-1">Pilih tanggal dan jam konsultasi yang tersedia.</p>
                        
                        <!-- Date Filter Row -->
                        <div class="flex gap-3 items-end mt-6">
                            <div class="flex-1">
                                <label for="date-picker-input" class="block text-xxs font-bold text-gray-600 uppercase tracking-wider mb-2">Filter Tanggal</label>
                                <input type="date" id="date-picker-input" value="{{ request('tanggal') }}" class="w-full bg-white border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11">
                            </div>
                            <button type="button" onclick="document.getElementById('filter-tanggal-hidden').value = document.getElementById('date-picker-input').value; document.getElementById('filter-form').submit();" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm h-11 px-6 rounded-xl transition shadow-md shadow-blue-900/20">
                                Cari Slot
                            </button>
                        </div>
                    </div>
                    
                    <!-- Slot list -->
                    <div class="p-6 sm:p-8 space-y-4 max-h-[550px] overflow-y-auto">
                        <x-input-error :messages="$errors->get('id_jadwal')" class="mb-4" />

                        @forelse ($jadwalKonsultasi as $jadwal)
                            @php
                                $isTersedia = $jadwal->status_slot === 'tersedia';
                            @endphp
                            
                            <label class="group relative block cursor-pointer {{ $isTersedia ? '' : 'pointer-events-none opacity-70' }}">
                                <input type="radio" name="id_jadwal" value="{{ $jadwal->id_jadwal }}" class="peer sr-only" @checked((string) old('id_jadwal') === (string) $jadwal->id_jadwal) @disabled(!$isTersedia)>
                                
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 sm:p-5 rounded-xl border transition-all duration-200
                                    {{ $isTersedia ? 'bg-[#F8FAFC] border-[#E2E8F0] group-hover:border-accent-blue peer-checked:bg-[#EFF6FF] peer-checked:border-accent-blue peer-checked:ring-1 peer-checked:ring-accent-blue' : 'bg-gray-50 border-gray-200' }}">
                                    
                                    <div class="space-y-1">
                                        <div class="font-bold text-navy-dark sm:text-base text-sm peer-checked:text-accent-blue transition-colors">
                                            {{ $jadwal->tanggal?->translatedFormat('l, d F Y') ?? '-' }}
                                        </div>
                                        <div class="text-gray-500 text-xs sm:text-sm font-medium">
                                            {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} – {{ substr((string) $jadwal->waktu_selesai, 0, 5) }} WIB
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 sm:mt-0 flex flex-row sm:flex-row items-center gap-3">
                                        <x-status-badge :status="$jadwal->status_slot" />
                                        
                                        @if ($isTersedia)
                                            <div class="bg-white border border-[#E2E8F0] group-hover:bg-gray-50 peer-checked:bg-accent-blue peer-checked:border-accent-blue peer-checked:text-white text-gray-700 font-bold text-xs px-4 py-2 sm:px-5 sm:py-2.5 rounded-lg sm:rounded-xl transition-all shadow-sm whitespace-nowrap">
                                                <span class="group-peer-checked:hidden block peer-checked:hidden">Pilih Slot</span>
                                                <span class="hidden peer-checked:block">Dipilih ✓</span>
                                            </div>
                                        @else
                                            <div class="bg-white border border-[#E2E8F0] text-gray-400 font-bold text-xs px-4 py-2 sm:px-5 sm:py-2.5 rounded-lg sm:rounded-xl opacity-50 whitespace-nowrap">
                                                Tidak Tersedia
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-12 px-4 border-2 border-dashed border-[#E2E8F0] rounded-2xl bg-[#F8FAFC]">
                                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-bold text-navy-dark">Belum ada slot jadwal</h3>
                                <p class="mt-1 text-xs text-gray-500">Admin belum menambahkan jadwal konsultasi yang tersedia untuk saat ini.</p>
                            </div>
                        @endforelse
                        
                        @if($jadwalKonsultasi->hasPages())
                            <div class="pt-4 border-t border-[#F1F5F9]">
                                {{ $jadwalKonsultasi->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- RIGHT COLUMN: Detail Konsultasi -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between min-h-[500px]">
                    <div class="p-6 sm:p-8 space-y-6">
                        <!-- Section Title -->
                        <div class="border-b border-[#F1F5F9] pb-4">
                            <h3 class="font-bold text-navy-dark text-lg">Detail Konsultasi</h3>
                            <p class="text-xs text-gray-500 mt-1">Pilih metode konsultasi yang Anda inginkan. Detail teknis akan dikonfirmasi oleh Admin.</p>
                        </div>

                        <!-- Method selector -->
                        <div>
                            <x-input-label for="metode_konsultasi" :value="__('Metode Konsultasi')" class="!text-xs !font-bold !text-gray-600 !uppercase !tracking-wider mb-3" />
                            <div class="flex gap-4">
                                <label class="cursor-pointer group relative flex items-center justify-center bg-[#F8FAFC] border border-[#E2E8F0] hover:border-accent-blue rounded-xl px-4 py-3 flex-1 transition shadow-sm has-[:checked]:border-accent-blue has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-accent-blue">
                                    <input type="radio" name="metode_konsultasi" value="online" class="peer sr-only" @checked(old('metode_konsultasi') === 'online')>
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-400 peer-checked:text-accent-blue group-hover:text-accent-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-bold text-sm text-gray-600 peer-checked:text-navy-dark">Online</span>
                                    </div>
                                </label>

                                <label class="cursor-pointer group relative flex items-center justify-center bg-[#F8FAFC] border border-[#E2E8F0] hover:border-accent-blue rounded-xl px-4 py-3 flex-1 transition shadow-sm has-[:checked]:border-accent-blue has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-accent-blue">
                                    <input type="radio" name="metode_konsultasi" value="offline" class="peer sr-only" @checked(old('metode_konsultasi', 'offline') === 'offline')>
                                    <div class="flex items-center gap-2">
                                        <svg class="h-5 w-5 text-gray-400 peer-checked:text-accent-blue group-hover:text-accent-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        <span class="font-bold text-sm text-gray-600 peer-checked:text-navy-dark">Offline</span>
                                    </div>
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('metode_konsultasi')" class="mt-2" />
                        </div>

                        <!-- Catatan Preferensi -->
                        <div>
                            <x-input-label for="catatan_preferensi_klien" :value="__('Catatan Preferensi (Opsional)')" class="!text-xs !font-bold !text-gray-600 !uppercase !tracking-wider mb-2" />
                            <textarea id="catatan_preferensi_klien" name="catatan_preferensi_klien" rows="4" class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm resize-none" placeholder="Tuliskan preferensi atau informasi tambahan terkait konsultasi...">{{ old('catatan_preferensi_klien') }}</textarea>
                            <x-input-error :messages="$errors->get('catatan_preferensi_klien')" class="mt-2" />
                        </div>

                        <!-- Alert Aturan Booking -->
                        <div class="bg-[#EFF6FF] border-l-4 border-accent-blue p-4 rounded-r-xl">
                            <div class="flex gap-2 items-center">
                                <svg class="h-5 w-5 text-accent-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-bold text-accent-blue text-sm">Aturan Booking</span>
                            </div>
                            <p class="text-xs text-blue-700/80 mt-2 pl-7 leading-relaxed">
                                Satu pengajuan hanya dapat memiliki satu booking aktif. Link atau lokasi konsultasi akan dikonfirmasi oleh Admin.
                            </p>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="p-6 sm:p-8 border-t border-[#F1F5F9] bg-[#F8FAFC]/50 flex items-center justify-end gap-3 shrink-0">
                        <a href="{{ route('klien.pra-pendaftaran.show', $praPendaftaranPerkara) }}" class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm">
                            Batal
                        </a>
                        <button type="submit" 
                                :disabled="isSubmitting"
                                class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isSubmitting">Konfirmasi Booking</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengirim...</span>
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</x-app-layout>
