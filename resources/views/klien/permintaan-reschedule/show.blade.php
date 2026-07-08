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
            <span class="text-gray-600 font-mono">RS-{{ str_pad($permintaanReschedule->id_reschedule, 3, '0', STR_PAD_LEFT) }}</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
            {{ __('Detail Permintaan Reschedule') }}
        </h2>
    </x-slot>

    @php
        $bookingLama = $permintaanReschedule->bookingLama;
        $bookingBaru = $permintaanReschedule->bookingBaru;
        $pengajuan = $bookingLama?->praPendaftaranPerkara;
        $jadwalLama = $bookingLama?->jadwalKonsultasi;
        $jadwalBaru = $permintaanReschedule->jadwalBaru ?? $bookingBaru?->jadwalKonsultasi;
        $statusColor = match ($permintaanReschedule->status_reschedule) {
            'disetujui' => 'green',
            'ditolak' => 'red',
            default => 'yellow',
        };
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

        <!-- Status Alerts Banner -->
        @if ($permintaanReschedule->status_reschedule === 'menunggu_persetujuan')
            <div class="bg-[#FFFBEB] border-l-4 border-[#F59E0B] p-4 rounded-r-xl border border-y-[#F59E0B]/20 border-r-[#F59E0B]/20 shadow-sm">
                <div class="flex gap-2 items-center">
                    <svg class="h-5 w-5 text-[#D97706] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-bold text-[#92400E] text-sm">Menunggu Persetujuan</span>
                </div>
                <p class="text-xs text-[#92400E]/80 mt-2 pl-7 leading-relaxed">
                    Permintaan reschedule sedang menunggu persetujuan Admin. Jadwal konsultasi yang lama tetap berlaku sampai Admin memberikan keputusan.
                </p>
            </div>
        @elseif ($permintaanReschedule->status_reschedule === 'ditolak')
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl border border-y-red-200 border-r-red-200 shadow-sm">
                <div class="flex gap-2 items-center">
                    <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-bold text-red-700 text-sm">Permintaan Ditolak</span>
                </div>
                <p class="text-xs text-red-700/80 mt-2 pl-7 leading-relaxed">
                    Permintaan reschedule Anda ditolak oleh Admin. Jadwal lama Anda tetap berlaku. Silakan ikuti konsultasi sesuai jadwal sebelumnya.
                </p>
            </div>
        @elseif ($permintaanReschedule->status_reschedule === 'disetujui')
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl border border-y-green-200 border-r-green-200 shadow-sm">
                <div class="flex gap-2 items-center">
                    <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-bold text-green-700 text-sm">Permintaan Disetujui</span>
                </div>
                <p class="text-xs text-green-700/80 mt-2 pl-7 leading-relaxed">
                    Permintaan reschedule disetujui. Booking lama telah dibatalkan dan sistem telah mengalihkan jadwal Anda ke jadwal baru.
                </p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            <!-- Left Card: Detail Reschedule -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                    <h3 class="font-bold text-navy-dark text-lg">Informasi Permintaan</h3>
                </div>
                
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="border-b border-[#F1F5F9]/60 pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Permintaan</span>
                        <x-status-badge :status="$permintaanReschedule->status_reschedule" :color="$statusColor" />
                    </div>

                    <div class="border-b border-[#F1F5F9]/60 pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                        <span class="text-sm font-bold text-navy-dark">{{ $permintaanReschedule->tanggal_pengajuan?->translatedFormat('d M Y • H:i') ?? '-' }} WIB</span>
                    </div>

                    <div class="border-b border-[#F1F5F9]/60 pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Preferensi Metode Baru</span>
                        <span class="text-sm font-semibold text-gray-700 uppercase">{{ $permintaanReschedule->preferensi_metode ?: 'Metode Lama' }}</span>
                    </div>

                    <div class="space-y-2 border-b border-[#F1F5F9]/60 pb-4">
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Preferensi Jadwal Baru</span>
                        <span class="text-sm font-semibold text-navy-dark leading-relaxed">{{ $permintaanReschedule->preferensi_jadwal ?: '-' }}</span>
                    </div>

                    <div class="space-y-2 border-b border-[#F1F5F9]/60 pb-4">
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alasan Reschedule</span>
                        <p class="text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl whitespace-pre-line">
                            {{ $permintaanReschedule->alasan_reschedule }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan Admin</span>
                        <p class="text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl whitespace-pre-line">
                            {{ $permintaanReschedule->catatan_admin ?: 'Belum ada catatan dari Admin.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right Card: Perbandingan Jadwal -->
            <div class="space-y-6">
                <!-- Card Jadwal Lama -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                        <h3 class="font-bold text-navy-dark text-lg">Jadwal Lama</h3>
                    </div>
                    <div class="p-6 sm:p-8 space-y-4">
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Judul Perkara</span>
                            <span class="block text-sm font-bold text-navy-dark mt-0.5">{{ $pengajuan?->judul_perkara ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Konsultasi</span>
                            <span class="block text-sm font-semibold text-navy-dark mt-0.5">
                                {{ $jadwalLama?->tanggal?->translatedFormat('l, d M Y') ?? '-' }}
                                @if ($jadwalLama)
                                    • {{ substr((string) $jadwalLama->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalLama->waktu_selesai, 0, 5) }} WIB
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Status Booking</span>
                            <x-status-badge :status="$bookingLama?->status_booking ?? '-'" :color="$bookingLama?->status_booking === 'aktif' ? 'green' : 'gray'" />
                        </div>
                    </div>
                </div>

                <!-- Card Jadwal Baru (Jika Ada) -->
                @if ($jadwalBaru || $bookingBaru)
                    <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                            <h3 class="font-bold text-navy-dark text-lg">Jadwal Baru (Disetujui)</h3>
                        </div>
                        <div class="p-6 sm:p-8 space-y-4">
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Konsultasi Baru</span>
                                <span class="block text-sm font-bold text-navy-dark mt-0.5">
                                    {{ $jadwalBaru?->tanggal?->translatedFormat('l, d M Y') ?? '-' }}
                                    @if ($jadwalBaru)
                                        • {{ substr((string) $jadwalBaru->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalBaru->waktu_selesai, 0, 5) }} WIB
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-1.5">Status Booking Baru</span>
                                <x-status-badge :status="$bookingBaru?->status_booking ?? '-'" :color="$bookingBaru?->status_booking === 'aktif' ? 'green' : 'gray'" />
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Footer Actions -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm flex items-center justify-end gap-3">
            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-sm">
                Kembali ke Detail Pengajuan
            </a>
        </div>
    </div>
</x-app-layout>
