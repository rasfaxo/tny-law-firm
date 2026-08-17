<x-app-layout title="Detail Konsultasi" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Jadwal Konsultasi', 'url' => route('klien.booking-konsultasi.index')], ['label' => 'BK-' . str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT)]]">

    @php
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
        $perkara = $bookingKonsultasi->praPendaftaranPerkara;
        $permintaanMenunggu = $bookingKonsultasi->permintaanReschedule
            ->firstWhere('status_reschedule', 'menunggu_persetujuan');
            
        $bisaAjukanReschedule = $bookingKonsultasi->status_booking === 'aktif' 
            && $perkara->status_pengajuan === 'jadwal_dipilih' 
            && !$permintaanMenunggu;
    @endphp

    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Notifikasi -->
        @if (session('success'))
            <x-alert-banner type="success">
                {{ session('success') }}
            </x-alert-banner>
        @endif

        @if (session('error'))
            <x-alert-banner type="error">
                {{ session('error') }}
            </x-alert-banner>
        @endif

        <!-- Banner status reschedule -->
        @if($permintaanMenunggu)
            <x-alert-banner type="warning" title="Permintaan Reschedule Menunggu Persetujuan">
                Anda telah mengajukan reschedule untuk booking ini. Jadwal lama tetap berlaku hingga keputusan disetujui Admin.
            </x-alert-banner>
        @endif

        <!-- 1. Header Card -->
        <x-card>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4">
                <span class="inline-flex bg-blue-50 text-accent-blue font-bold font-mono text-xs px-3 py-1 rounded-lg w-max">
                    BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }}
                </span>
                
                @if($bisaAjukanReschedule)
                    <a href="{{ route('klien.permintaan-reschedule.create', $bookingKonsultasi) }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-bold text-xs px-4 py-2 rounded-xl transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Request Reschedule
                    </a>
                @endif
            </div>

            <h3 class="font-extrabold text-navy-dark text-2xl sm:text-3xl leading-tight mt-4">
                {{ $perkara->judul_perkara }}
            </h3>
            
            <x-divider />

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">STATUS BOOKING</span>
                    <x-status-badge :status="$bookingKonsultasi->status_booking" />
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">STATUS KONFIRMASI</span>
                    <x-status-badge :status="$bookingKonsultasi->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi'" />
                </div>
                <div>
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">METODE KONSULTASI</span>
                    <x-status-badge :status="$bookingKonsultasi->metode_konsultasi" />
                </div>
            </div>
        </x-card>

        <!-- 2. Jadwal Konsultasi Card -->
        <x-card>
            <h3 class="font-bold text-navy-dark text-xl">Jadwal Konsultasi</h3>
            
            <x-divider />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Tanggal -->
                <div class="bg-[#F8FAFC] rounded-xl p-5 border border-[#E2E8F0]">
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">TANGGAL</span>
                    <div class="flex items-center gap-3 text-sm font-bold text-navy-dark">
                        <svg class="h-5 w-5 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>{{ $jadwal?->tanggal?->translatedFormat('l, d M Y') ?? '-' }}</span>
                    </div>
                </div>

                <!-- Jam Mulai -->
                <div class="bg-[#F8FAFC] rounded-xl p-5 border border-[#E2E8F0]">
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">JAM MULAI</span>
                    <div class="flex items-center gap-3 text-sm font-bold text-navy-dark">
                        <svg class="h-5 w-5 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) : '-' }} WIB</span>
                    </div>
                </div>

                <!-- Jam Selesai -->
                <div class="bg-[#F8FAFC] rounded-xl p-5 border border-[#E2E8F0]">
                    <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">JAM SELESAI</span>
                    <div class="flex items-center gap-3 text-sm font-bold text-navy-dark">
                        <svg class="h-5 w-5 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ $jadwal ? substr((string) $jadwal->waktu_selesai, 0, 5) : '-' }} WIB</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- 3. Informasi Admin Card -->
        <x-card>
            <div>
                <h3 class="font-bold text-navy-dark text-xl">Informasi Admin</h3>
                <p class="text-sm text-gray-500 mt-1">Informasi yang diberikan oleh Admin untuk pelaksanaan konsultasi.</p>
            </div>

            <x-divider />

            <div class="divide-y divide-[#E2E8F0] -my-4">
                <!-- Link / Lokasi -->
                <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] items-start gap-2 sm:gap-6 py-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-1">
                        {{ $bookingKonsultasi->metode_konsultasi === 'online' ? 'LINK KONSULTASI' : 'LOKASI PERTEMUAN' }}
                    </span>
                    <div>
                        @if($bookingKonsultasi->metode_konsultasi === 'online')
                            @if($bookingKonsultasi->link_konsultasi)
                                <a href="{{ $bookingKonsultasi->link_konsultasi }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-semibold text-accent-blue hover:text-blue-800 hover:underline transition">
                                    <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="break-all">{{ $bookingKonsultasi->link_konsultasi }}</span>
                                </a>
                            @else
                                <span class="text-sm font-medium text-gray-400 italic">Belum disediakan.</span>
                            @endif
                        @else
                            <p class="text-sm font-medium text-navy-dark leading-relaxed">
                                {{ $bookingKonsultasi->lokasi_konsultasi ?: 'Belum disediakan.' }}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Catatan Admin -->
                <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] items-start gap-2 sm:gap-6 py-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-1">CATATAN ADMIN</span>
                    <p class="text-sm font-medium text-navy-dark leading-relaxed whitespace-pre-line">
                        {{ $bookingKonsultasi->catatan_konsultasi ?: '-' }}
                    </p>
                </div>
            </div>
        </x-card>

        <!-- 4. Catatan Klien Card -->
        <x-card>
            <div>
                <h3 class="font-bold text-navy-dark text-xl">Catatan Preferensi Klien</h3>
                <p class="text-sm text-gray-500 mt-1">Catatan yang Anda berikan saat mengajukan booking jadwal konsultasi.</p>
            </div>

            <x-divider />

            <div class="grid grid-cols-1 sm:grid-cols-[200px_1fr] items-start gap-2 sm:gap-6">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider pt-1">CATATAN PREFERENSI</span>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-5 rounded-xl">
                    <p class="text-sm text-navy-dark font-medium leading-relaxed whitespace-pre-line">
                        {{ $bookingKonsultasi->catatan_preferensi_klien ?: 'Klien tidak menyertakan preferensi khusus.' }}
                    </p>
                </div>
            </div>
        </x-card>

        <!-- 5. Request Reschedule Action Card -->
        <x-card class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div class="space-y-1">
                <h3 class="font-bold text-navy-dark text-lg">Request Reschedule</h3>
                <p class="text-sm text-gray-500">Jadwal tetap berlaku hingga disetujui oleh Admin.</p>
            </div>
            
            <div class="shrink-0">
                @if($bisaAjukanReschedule)
                    <x-primary-button href="{{ route('klien.permintaan-reschedule.create', $bookingKonsultasi) }}" tag="a" class="w-full sm:w-auto justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Request Reschedule
                    </x-primary-button>
                @else
                    <button disabled class="bg-gray-100 text-gray-400 font-bold text-sm px-6 py-3 rounded-xl border border-gray-200 cursor-not-allowed inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Reschedule Menunggu
                    </button>
                @endif
            </div>
        </x-card>

        <!-- 6. Back Button Area -->
        <div class="pt-4 pb-12">
            <x-secondary-button href="{{ route('klien.booking-konsultasi.index') }}" tag="a">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Jadwal
            </x-secondary-button>
        </div>
    </div>
</x-app-layout>
