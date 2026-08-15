<x-app-layout title="Dashboard Klien" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Dashboard']]">

    <div class="space-y-6">
        <!-- Jumbotron Sambutan -->
        <div class="bg-gradient-to-r from-navy-dark to-navy-primary rounded-2xl shadow-lg p-6 md:p-8 text-white relative overflow-hidden flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <!-- Subtle decorative background ornaments -->
            <div class="absolute bg-white/5 rounded-full w-[250px] h-[250px] -top-20 -left-20 blur-2xl"></div>
            <div class="absolute bg-accent-blue/15 rounded-full w-[180px] h-[180px] -bottom-10 -right-10 blur-3xl"></div>

            <div class="space-y-2 z-10">
                <h3 class="text-xl md:text-2xl font-bold">Selamat datang kembali, {{ Auth::user()->nama }}</h3>
                <p class="text-sm text-gray-300 max-w-xl leading-relaxed">
                    Pantau status pra-pendaftaran perkara Anda secara real-time, kelola unggahan dokumen, dan tentukan jadwal konsultasi hukum Anda di sini.
                </p>
            </div>

            <div class="shrink-0 z-10">
                <a href="{{ route('klien.pra-pendaftaran.create') }}" class="bg-[#d4af37] text-navy-dark hover:bg-[#c5a02e] hover:shadow-lg transition duration-200 px-6 py-3 rounded-xl font-bold text-sm tracking-wide flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Buat Pengajuan Perkara</span>
                </a>
            </div>
        </div>

        <!-- Statistik Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Total Pengajuan -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-indigo-50 p-2.5 rounded-xl text-indigo-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">
                        {{ $statistics['Total Pengajuan Saya'] ?? 0 }}
                    </span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-[#334155]">Total Pengajuan</span>
                    <span class="block text-xs text-gray-400">Semua perkara yang diajukan</span>
                </div>
            </x-card>

            <!-- Card 2: Menunggu Verifikasi -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-yellow-50 p-2.5 rounded-xl text-yellow-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">
                        {{ ($statistics['Pengajuan Menunggu Verifikasi'] ?? 0) + ($statistics['Reschedule Menunggu Persetujuan'] ?? 0) }}
                    </span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-[#334155]">Menunggu Verifikasi</span>
                    <span class="block text-xs text-gray-400">Sedang diperiksa Staf Legal</span>
                </div>
            </x-card>

            <!-- Card 3: Berkas Lengkap -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-green-50 p-2.5 rounded-xl text-green-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">
                        {{ $statistics['Pengajuan Berkas Lengkap'] ?? 0 }}
                    </span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-[#334155]">Berkas Lengkap</span>
                    <span class="block text-xs text-gray-400">Siap memilih jadwal pertemuan</span>
                </div>
            </x-card>

            <!-- Card 4: Jadwal Dipilih -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-blue-50 p-2.5 rounded-xl text-accent-blue">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">
                        {{ $statistics['Pengajuan Jadwal Dipilih'] ?? 0 }}
                    </span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-[#334155]">Jadwal Dipilih</span>
                    <span class="block text-xs text-gray-400">Menunggu pelaksanaan konsultasi</span>
                </div>
            </x-card>
        </div>

        <!-- 2-Column Dashboard Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Kolom Kiri: Pengajuan Terbaru & Reschedule (lg:col-span-8) -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Card Pengajuan Terbaru -->
                <x-card class="p-0 overflow-hidden sm:p-0">
                    <div class="p-6 border-b border-[#F1F5F9] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-navy-dark text-base">Pengajuan Terbaru</h4>
                            <p class="text-xs text-gray-400 mt-1">Daftar pengajuan pra-pendaftaran perkara Anda terakhir</p>
                        </div>
                        <a href="{{ route('klien.pra-pendaftaran.index') }}" class="text-xs font-semibold text-accent-blue hover:text-navy-dark transition">
                            Lihat Semua Pengajuan &rarr;
                        </a>
                    </div>

                    <!-- Desktop Table Layout -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#F1F5F9]">
                            <thead class="bg-[#F8FAFC]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#F1F5F9]">
                                @forelse ($pengajuanTerbaru as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold font-mono text-navy-primary">
                                            PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy-dark">
                                            {{ \Illuminate\Support\Str::limit($pengajuan->judul_perkara, 25) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$pengajuan->status_pengajuan" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                            {{ $pengajuan->tanggal_pengajuan?->format('d M Y') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-accent-blue hover:underline transition">
                                                <span>Detail</span>
                                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <x-empty-state title="Belum Ada Pengajuan" message="Belum ada pengajuan pra-pendaftaran perkara." />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Layout -->
                    <div class="block md:hidden divide-y divide-[#F1F5F9] bg-white">
                        @forelse ($pengajuanTerbaru as $pengajuan)
                            <div class="p-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-bold font-mono text-navy-primary">
                                        PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-navy-dark text-sm">{{ $pengajuan->judul_perkara }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">Kategori: {{ $pengajuan->kategori?->nama_kategori ?? '-' }}</p>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                    <span class="text-xs text-gray-400 font-medium">{{ $pengajuan->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                                    <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-400">
                                Belum ada pengajuan pra-pendaftaran perkara.
                            </div>
                        @endforelse
                    </div>
                </x-card>

                <!-- Card Permintaan Reschedule Saya -->
                <x-card class="p-0 overflow-hidden sm:p-0">
                    <div class="p-6 border-b border-[#F1F5F9]">
                        <h4 class="font-bold text-navy-dark text-base">Permintaan Reschedule Terbaru</h4>
                        <p class="text-xs text-gray-400 mt-1">Status pengajuan permohonan pemindahan jadwal konsultasi Anda</p>
                    </div>

                    <!-- Desktop Table Layout -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#F1F5F9]">
                            <thead class="bg-[#F8FAFC]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Perkara</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status Reschedule</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Diajukan Pada</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan Admin</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#F1F5F9]">
                                @forelse ($permintaanRescheduleSaya as $reschedule)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy-dark">
                                            {{ \Illuminate\Support\Str::limit($reschedule->bookingLama?->praPendaftaranPerkara?->judul_perkara ?? '-', 20) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$reschedule->status_reschedule" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                            {{ $reschedule->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-[200px] truncate">
                                            {{ $reschedule->catatan_admin ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('klien.permintaan-reschedule.show', $reschedule) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-accent-blue hover:underline transition">
                                                <span>Detail</span>
                                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <x-empty-state title="Belum Ada Permintaan Reschedule" message="Belum ada permintaan reschedule jadwal." />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Mobile Card Layout -->
                    <div class="block md:hidden divide-y divide-[#F1F5F9] bg-white">
                        @forelse ($permintaanRescheduleSaya as $reschedule)
                            <div class="p-4 space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-semibold text-gray-400 font-mono">Reschedule</span>
                                    <x-status-badge :status="$reschedule->status_reschedule" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-navy-dark text-sm">
                                        {{ $reschedule->bookingLama?->praPendaftaranPerkara?->judul_perkara ?? '-' }}
                                    </h4>
                                    @if($reschedule->catatan_admin)
                                        <p class="text-xs text-gray-500 mt-1 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                            <strong>Catatan Admin:</strong> {{ $reschedule->catatan_admin }}
                                        </p>
                                    @endif
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                                    <span class="text-xs text-gray-400 font-medium">Diajukan: {{ $reschedule->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                                    <a href="{{ route('klien.permintaan-reschedule.show', $reschedule) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-400">
                                Belum ada permintaan reschedule jadwal.
                            </div>
                        @endforelse
                    </div>
                </x-card>
            </div>

            <!-- Kolom Kanan: Jadwal Konsultasi Saya (lg:col-span-4) -->
            <div class="lg:col-span-4">
                <x-card class="flex flex-col space-y-4">
                    <div class="border-b border-[#F1F5F9] pb-3">
                        <h4 class="font-bold text-navy-dark text-base">Jadwal Konsultasi Saya</h4>
                        <p class="text-xs text-gray-400 mt-1">Status pertemuan yang sedang aktif saat ini</p>
                    </div>

                    @if ($booking = $bookingAktif->first())
                        @php
                            $jadwal = $booking->jadwalKonsultasi;
                        @endphp
                        <div class="flex items-center gap-2 flex-wrap">
                            <x-status-badge :status="$booking->status_booking" />
                            <x-status-badge :status="$booking->status_konfirmasi_konsultasi" />
                        </div>

                        <!-- Date Box -->
                        <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl space-y-2">
                            <div class="flex items-center gap-2 text-sm font-semibold text-[#334155]">
                                <svg class="h-4 w-4 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $jadwal?->tanggal?->format('l, d M Y') ?? '-' }}</span>
                            </div>
                            <div class="pl-6 text-xs text-gray-500 font-medium">
                                {{ $jadwal ? $jadwal->waktu_mulai . ' – ' . $jadwal->waktu_selesai . ' WIB' : '-' }}
                            </div>
                        </div>

                        <!-- Case Info & Method -->
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center gap-2">
                                <x-status-badge :status="$booking->metode_konsultasi" />
                                <span class="text-sm font-medium text-navy-dark truncate max-w-[180px]">
                                    {{ $booking->praPendaftaranPerkara?->judul_perkara ?? '-' }}
                                </span>
                            </div>

                            @if($booking->status_konfirmasi_konsultasi === 'terkonfirmasi')
                                <div class="bg-green-50/50 border border-green-100 p-3 rounded-xl text-xs text-green-700 leading-relaxed">
                                    <strong>Lokasi / Link Pertemuan:</strong><br>
                                    @if($booking->metode_konsultasi === 'online')
                                        <a href="{{ $booking->link_konsultasi }}" target="_blank" class="text-accent-blue hover:underline break-all font-semibold">
                                            {{ $booking->link_konsultasi }}
                                        </a>
                                    @else
                                        <span class="font-medium text-gray-700">{{ $booking->lokasi_konsultasi ?? '-' }}</span>
                                    @endif
                                    @if($booking->catatan_admin)
                                        <div class="mt-1 text-gray-500 italic">"{{ $booking->catatan_admin }}"</div>
                                    @endif
                                </div>
                            @else
                                <div class="bg-yellow-50/50 border border-yellow-100 p-3 rounded-xl text-xs text-yellow-800 leading-relaxed">
                                    Informasi teknis konsultasi sedang menunggu konfirmasi Admin. Tautan online atau alamat kantor akan tampil di sini setelah disetujui.
                                </div>
                            @endif
                        </div>

                        <!-- Actions Buttons -->
                        <div class="pt-4 flex flex-col gap-2">
                            <x-primary-button href="{{ route('klien.pra-pendaftaran.show', $booking->praPendaftaranPerkara) }}" tag="a" class="justify-center">
                                Detail Pengajuan
                            </x-primary-button>
                            <x-secondary-button href="{{ route('klien.permintaan-reschedule.create', $booking) }}" tag="a" class="justify-center gap-2">
                                <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span>Ajukan Reschedule</span>
                            </x-secondary-button>
                        </div>
                    @else
                        <!-- Empty State Jadwal -->
                        <div class="py-4">
                            <x-empty-state title="Tidak Ada Jadwal Aktif" message="Jadwal dapat dipilih jika status pengajuan perkara hukum Anda sudah berstatus Berkas Lengkap." />
                        </div>
                    @endif
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
