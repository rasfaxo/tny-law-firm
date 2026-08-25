<x-app-layout title="Detail Permintaan Reschedule" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Jadwal Konsultasi', 'url' => route('klien.booking-konsultasi.index')], ['label' => 'RS-' . str_pad($permintaanReschedule->id_reschedule, 3, '0', STR_PAD_LEFT)]]">

    @php
        $bookingLama = $permintaanReschedule->bookingLama;
        $bookingBaru = $permintaanReschedule->bookingBaru;
        $pengajuan = $bookingLama?->praPendaftaranPerkara;
        $jadwalLama = $bookingLama?->jadwalKonsultasi;
        $jadwalBaru = $permintaanReschedule->jadwalBaru ?? $bookingBaru?->jadwalKonsultasi;
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <x-alert-banner type="success">
                {{ session('success') }}
            </x-alert-banner>
        @endif

        <!-- Status Alerts Banner -->
        @if ($permintaanReschedule->status_reschedule === 'menunggu_persetujuan')
            <x-alert-banner type="warning" title="Menunggu Persetujuan">
                Permintaan reschedule sedang menunggu persetujuan Admin. Jadwal konsultasi yang lama tetap berlaku sampai Admin memberikan keputusan.
            </x-alert-banner>
        @elseif ($permintaanReschedule->status_reschedule === 'ditolak')
            <x-alert-banner type="error" title="Permintaan Ditolak">
                Permintaan reschedule Anda ditolak oleh Admin. Jadwal lama Anda tetap berlaku. Silakan ikuti konsultasi sesuai jadwal sebelumnya.
            </x-alert-banner>
        @elseif ($permintaanReschedule->status_reschedule === 'disetujui')
            <x-alert-banner type="success" title="Permintaan Disetujui">
                Permintaan reschedule disetujui. Booking lama telah dibatalkan dan sistem telah mengalihkan jadwal Anda ke jadwal baru.
            </x-alert-banner>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            
            <!-- Left Card: Detail Reschedule -->
            <x-card class="p-0 overflow-hidden sm:p-0">
                <div class="p-6 sm:p-8 border-b border-[#E2E8F0] bg-[#F8FAFC]">
                    <h3 class="font-bold text-navy-dark text-lg">Informasi Permintaan</h3>
                </div>
                
                <div class="p-6 sm:p-8 space-y-6">
                    <div class="border-b border-[#E2E8F0] pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Permintaan</span>
                        <x-status-badge :status="$permintaanReschedule->status_reschedule" />
                    </div>

                    <div class="border-b border-[#E2E8F0] pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                        <span class="text-sm font-bold text-navy-dark">{{ $permintaanReschedule->tanggal_pengajuan?->translatedFormat('d M Y • H:i') ?? '-' }} WIB</span>
                    </div>

                    <div class="border-b border-[#E2E8F0] pb-4 flex justify-between items-center">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Preferensi Metode Baru</span>
                        <span class="text-sm font-semibold text-gray-700 uppercase">{{ $permintaanReschedule->preferensi_metode ?: 'Metode Lama' }}</span>
                    </div>

                    <div class="space-y-2 border-b border-[#E2E8F0] pb-4">
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Preferensi Jadwal Baru</span>
                        <span class="text-sm font-semibold text-navy-dark leading-relaxed">{{ $permintaanReschedule->preferensi_jadwal ?: '-' }}</span>
                    </div>

                    <div class="space-y-2 border-b border-[#E2E8F0] pb-4">
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
            </x-card>

            <!-- Right Card: Perbandingan Jadwal -->
            <div class="space-y-6">
                <!-- Card Jadwal Lama -->
                <x-card class="p-0 overflow-hidden sm:p-0">
                    <div class="p-6 sm:p-8 border-b border-[#E2E8F0] bg-[#F8FAFC]">
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
                            <x-status-badge :status="$bookingLama?->status_booking ?? '-'" />
                        </div>
                    </div>
                </x-card>

                <!-- Card Jadwal Baru (Jika Ada) -->
                @if ($jadwalBaru || $bookingBaru)
                    <x-card class="p-0 overflow-hidden sm:p-0">
                        <div class="p-6 sm:p-8 border-b border-[#E2E8F0] bg-[#F8FAFC]">
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
                                <x-status-badge :status="$bookingBaru?->status_booking ?? '-'" />
                            </div>
                        </div>
                    </x-card>
                @endif
            </div>

        </div>

        <!-- Footer Actions -->
        <x-card class="flex items-center justify-end gap-3">
            <x-secondary-button href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" tag="a">
                Kembali ke Detail Pengajuan
            </x-secondary-button>
        </x-card>
    </div>
</x-app-layout>
