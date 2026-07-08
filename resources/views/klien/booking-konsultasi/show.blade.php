<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1">
            <span>Klien</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('klien.booking-konsultasi.index') }}" class="hover:underline">Jadwal Konsultasi</a>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="font-mono">BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }}</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
            {{ __('Detail Konsultasi') }}
        </h2>
    </x-slot>

    @php
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
        $perkara = $bookingKonsultasi->praPendaftaranPerkara;
        $permintaanMenunggu = $bookingKonsultasi->permintaanReschedule
            ->firstWhere('status_reschedule', 'menunggu_persetujuan');
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 flex gap-3 text-sm text-green-700 shadow-sm">
                <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
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

        <!-- Banner status reschedule -->
        @if($permintaanMenunggu)
            <div class="bg-amber-50 border border-amber-200 p-4 rounded-xl flex gap-3 text-sm text-amber-800 shadow-sm">
                <svg class="h-5 w-5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="space-y-1">
                    <p class="font-bold">Permintaan Reschedule Menunggu Persetujuan</p>
                    <p class="text-xs text-amber-700/80">Anda telah mengajukan reschedule untuk booking ini. Jadwal lama tetap berlaku hingga keputusan disetujui Admin.</p>
                </div>
            </div>
        @endif

        <!-- Card info header pengajuan -->
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-start md:justify-between gap-6">
            <div class="space-y-4 max-w-2xl">
                <div>
                    <h3 class="font-extrabold text-navy-dark text-lg sm:text-xl leading-tight">
                        {{ $perkara->judul_perkara }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-2 leading-relaxed">
                        Pendaftaran perkara Anda sedang berada di tahap konsultasi aktif. Silakan ikuti detail petunjuk di bawah ini.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <x-status-badge :status="$bookingKonsultasi->status_booking" />
                    <x-status-badge status="berkas_lengkap" />
                </div>
            </div>
            
            <div class="flex flex-row md:flex-col gap-6 md:gap-4 md:text-right border-t md:border-t-0 pt-4 md:pt-0 border-gray-100 shrink-0">
                <div>
                    <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Kategori Perkara</span>
                    <span class="block text-sm font-bold text-navy-dark mt-1">{{ $perkara->kategori?->nama_kategori ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Kode Pengajuan</span>
                    <a href="{{ route('klien.pra-pendaftaran.show', $perkara) }}" class="block text-sm font-bold text-accent-blue hover:underline font-mono mt-1">PP-{{ str_pad($perkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Left Card: Informasi Booking -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                    <h3 class="font-bold text-navy-dark text-lg">Informasi Konsultasi</h3>
                </div>
                
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="border-b border-[#F1F5F9] pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kode Booking</span>
                        <span class="text-sm font-bold text-navy-dark font-mono">BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="border-b border-[#F1F5F9] pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Konsultasi</span>
                        <span class="text-sm font-bold text-navy-dark">{{ $jadwal?->tanggal?->translatedFormat('l, d F Y') ?? '-' }}</span>
                    </div>

                    <div class="border-b border-[#F1F5F9] pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu Konsultasi</span>
                        <span class="text-sm font-bold text-navy-dark">{{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) . ' - ' . substr((string) $jadwal->waktu_selesai, 0, 5) : '-' }} WIB</span>
                    </div>

                    <div class="border-b border-[#F1F5F9] pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Metode Konsultasi</span>
                        <span class="inline-flex text-xxs font-bold px-2 py-0.5 rounded uppercase tracking-wider
                            {{ $bookingKonsultasi->metode_konsultasi === 'online' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $bookingKonsultasi->metode_konsultasi }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Booking</span>
                        <x-status-badge :status="$bookingKonsultasi->status_booking" />
                    </div>
                </div>
            </div>

            <!-- Right Card: Link & Lokasi Pertemuan -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                    <h3 class="font-bold text-navy-dark text-lg">Catatan & Tautan Pertemuan</h3>
                </div>
                
                <div class="p-6 sm:p-8 space-y-6">
                    @if($bookingKonsultasi->metode_konsultasi === 'online')
                        <div class="space-y-2">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Tautan Konsultasi</span>
                            @if($bookingKonsultasi->link_konsultasi)
                                <a href="{{ $bookingKonsultasi->link_konsultasi }}" target="_blank" class="inline-flex items-center gap-2 text-sm font-bold text-accent-blue hover:underline bg-blue-50 px-4 py-2.5 rounded-xl border border-blue-200 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                    Gabung Zoom / Meet
                                </a>
                            @else
                                <span class="text-sm font-medium text-gray-500 italic">Belum disediakan oleh Admin.</span>
                            @endif
                        </div>
                    @else
                        <div class="space-y-2">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Lokasi Pertemuan</span>
                            @if($bookingKonsultasi->lokasi_konsultasi)
                                <p class="text-sm font-bold text-navy-dark bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl leading-relaxed">
                                    {{ $bookingKonsultasi->lokasi_konsultasi }}
                                </p>
                            @else
                                <span class="text-sm font-medium text-gray-500 italic">Alamat kantor akan segera dikonfirmasi.</span>
                            @endif
                        </div>
                    @endif

                    <div class="space-y-2">
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan Konsultasi Admin</span>
                        <div class="text-sm text-gray-600 bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl leading-relaxed whitespace-pre-line">
                            {{ $bookingKonsultasi->catatan_konsultasi ?: 'Belum ada catatan dari Admin.' }}
                        </div>
                    </div>

                    <div class="space-y-2">
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan Preferensi Klien</span>
                        <div class="text-sm text-gray-600 bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl leading-relaxed whitespace-pre-line">
                            {{ $bookingKonsultasi->catatan_preferensi_klien ?: 'Tidak menuliskan catatan preferensi.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reschedule Action Row -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <p class="font-bold text-navy-dark text-sm">Butuh melakukan perubahan jadwal?</p>
                <p class="text-xs text-gray-500">Anda dapat mengajukan permohonan reschedule konsultasi jika jadwal di atas bentrok.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('klien.booking-konsultasi.index') }}" class="text-sm font-bold text-gray-500 hover:text-navy-dark transition px-4 py-2">
                    Kembali
                </a>
                
                @if($bookingKonsultasi->status_booking === 'aktif' && $perkara->status_pengajuan === 'jadwal_dipilih' && !$permintaanMenunggu)
                    <a href="{{ route('klien.permintaan-reschedule.create', $bookingKonsultasi) }}" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 whitespace-nowrap">
                        Ajukan Reschedule
                    </a>
                @else
                    <button disabled class="bg-gray-100 text-gray-400 font-bold text-sm px-6 py-2.5 rounded-xl border border-gray-200 cursor-not-allowed whitespace-nowrap">
                        Ajukan Reschedule (Terkunci)
                    </button>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
