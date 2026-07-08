<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1">
            <span>Klien</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('klien.pra-pendaftaran.index') }}" class="hover:underline">Pengajuan</a>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-600 font-mono">PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
            {{ __('Detail Pengajuan Perkara') }}
        </h2>
    </x-slot>

    @php
        // Logika Data Booking Konsultasi
        $bookingAktif = $praPendaftaranPerkara->bookingAktif;
        $bookingTampil = $bookingAktif ?: $praPendaftaranPerkara->bookingTerakhir;
        $semuaPermintaanReschedule = $praPendaftaranPerkara->bookingKonsultasi
            ->flatMap(fn ($booking) => $booking->permintaanReschedule);
        $permintaanRescheduleTerakhir = $semuaPermintaanReschedule->sortByDesc('tanggal_pengajuan')->first();
        $permintaanRescheduleMenunggu = $bookingAktif
            ? $semuaPermintaanReschedule
                ->where('id_booking', $bookingAktif->id_booking)
                ->firstWhere('status_reschedule', 'menunggu_persetujuan')
            : null;
        $bisaAjukanReschedule = $bookingAktif
            && $bookingAktif->status_booking === 'aktif'
            && $praPendaftaranPerkara->status_pengajuan === 'jadwal_dipilih'
            && !$permintaanRescheduleMenunggu;

        // Logika Status Verifikasi Terakhir
        $verifikasiTerakhir = $praPendaftaranPerkara->verifikasiBerkas->first();
    @endphp

    <div class="space-y-6">
        <!-- Alert Notification -->
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 flex gap-3 text-sm text-green-700">
                <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex gap-3 text-sm text-red-700">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- 2-Column Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- COLUMN LEFT: Pengajuan Detail, Dokumen & Timeline (lg:col-span-8) -->
            <div class="lg:col-span-8 space-y-6">
                
                <!-- Card Header Pengajuan (Figma node-id=65:1062) -->
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="bg-blue-50 text-accent-blue font-bold font-mono text-xs px-3 py-1 rounded-lg">
                            PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                        </span>
                        <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                    </div>
                    <div>
                        <h3 class="font-extrabold text-navy-dark text-xl leading-tight">
                            {{ $praPendaftaranPerkara->judul_perkara }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">
                            Diajukan pada {{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d F Y • H:i') ?? '-' }} WIB
                        </p>
                    </div>
                </div>

                <!-- Card Detail Perkara (Data Pengajuan) -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-[#F1F5F9]">
                        <h4 class="font-bold text-navy-dark text-sm uppercase tracking-wider text-gray-400">Deskripsi Perkara</h4>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Kategori Perkara</span>
                            <span class="block text-sm font-semibold text-navy-dark mt-0.5">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Kronologi Perkara</span>
                            <p class="text-sm text-navy-dark mt-1 leading-relaxed whitespace-pre-line bg-gray-50/50 p-4 rounded-xl border border-gray-100">
                                {{ $praPendaftaranPerkara->kronologi }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Card Dokumen Aktif -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-[#F1F5F9] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h4 class="font-bold text-navy-dark text-base">Dokumen Pendukung Aktif</h4>
                            <p class="text-xs text-gray-400 mt-1">Daftar dokumen pendukung perkara yang saat ini aktif</p>
                        </div>
                        @if ($praPendaftaranPerkara->status_pengajuan === 'menunggu_verifikasi')
                            <a href="{{ route('klien.dokumen.create', $praPendaftaranPerkara) }}" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 inline-flex items-center gap-1.5">
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span>Upload Dokumen Baru</span>
                            </a>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#F1F5F9]">
                            <thead class="bg-[#F8FAFC]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</th>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Berkas</th>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Unggah</th>
                                    <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#F1F5F9]">
                                @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-navy-dark">
                                            {{ $dokumen->nama_dokumen }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                            {{ $dokumen->jenis_dokumen }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$dokumen->status_dokumen" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                            {{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a href="{{ route('klien.dokumen.show', $dokumen) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-accent-blue hover:underline transition">
                                                <span>Lihat/Unduh</span>
                                                <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">
                                            Belum ada dokumen aktif yang diunggah.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Card Catatan Perbaikan Dokumen (Hanya jika status berkas_tidak_lengkap) -->
                @if ($praPendaftaranPerkara->status_pengajuan === 'berkas_tidak_lengkap')
                    @php
                        $catatanPerbaikan = $praPendaftaranPerkara->verifikasiBerkas
                            ->flatMap(fn ($verifikasi) => $verifikasi->catatanVerifikasi);
                    @endphp
                    <div class="bg-white border border-red-100 rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-red-50 bg-red-50/30">
                            <h4 class="font-bold text-red-900 text-base">Catatan Perbaikan Dokumen</h4>
                            <p class="text-xs text-red-700 mt-1">Harap perbaiki dokumen bermasalah di bawah ini sesuai instruksi verifikator Staf Legal</p>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[#F1F5F9]">
                                <thead class="bg-[#F8FAFC]">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</th>
                                        <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Instruksi Catatan</th>
                                        <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Koreksi</th>
                                        <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-[#F1F5F9]">
                                    @forelse ($catatanPerbaikan as $catatan)
                                        @php
                                            $dokumenCatatan = $catatan->dokumenPerkara;
                                            $bisaUploadPerbaikan = $catatan->status_perbaikan === 'belum_diperbaiki'
                                                && $dokumenCatatan
                                                && $dokumenCatatan->status_dokumen === 'perlu_perbaikan';
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 text-sm font-semibold text-navy-dark">
                                                <div class="font-semibold">{{ $dokumenCatatan?->nama_dokumen ?? '-' }}</div>
                                                <div class="text-xs text-gray-400 font-normal mt-0.5">{{ $dokumenCatatan?->jenis_dokumen ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600 max-w-[250px] leading-relaxed whitespace-pre-line">
                                                {{ $catatan->isi_catatan }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$catatan->status_perbaikan" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                @if ($bisaUploadPerbaikan)
                                                    <a href="{{ route('klien.perbaikan-dokumen.create', $catatan) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-accent-blue hover:underline transition">
                                                        <span>Unggah Ulang</span>
                                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                                        </svg>
                                                    </a>
                                                @else
                                                    <span class="text-gray-400 font-medium text-xs">Selesai diperbaiki</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">
                                                Tidak ada catatan perbaikan dokumen spesifik.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Card Riwayat Histori Dokumen Lama (Read-Only) -->
                @if ($praPendaftaranPerkara->riwayatDokumen->isNotEmpty())
                    <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-[#F1F5F9]">
                            <h4 class="font-bold text-navy-dark text-base">Histori Dokumen Lama</h4>
                            <p class="text-xs text-gray-400 mt-1">Histori berkas lama sebelum dilakukan perbaikan (bersifat arsip/read-only)</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[#F1F5F9]">
                                <thead class="bg-[#F8FAFC]">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</th>
                                        <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                                        <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Lama</th>
                                        <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Ganti</th>
                                        <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-[#F1F5F9]">
                                    @foreach ($praPendaftaranPerkara->riwayatDokumen as $riwayatDok)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                                {{ $riwayatDok->nama_dokumen }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                                {{ $riwayatDok->jenis_dokumen }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <x-status-badge :status="$riwayatDok->status_dokumen" />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                                {{ $riwayatDok->created_at?->format('d M Y') ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <a href="{{ route('klien.dokumen.show', $riwayatDok) }}" class="text-gray-400 hover:text-navy-dark transition text-xs font-semibold">
                                                    Lihat Berkas
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Card Riwayat Status (Timeline vertikal minimalis) -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-[#F1F5F9]">
                        <h4 class="font-bold text-navy-dark text-base">Timeline Riwayat Status</h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-0">
                            @forelse ($praPendaftaranPerkara->riwayatStatus as $index => $riwayat)
                                @php
                                    $isLast = $loop->last;
                                @endphp
                                <div class="flex gap-4 items-start relative">
                                    <!-- Marker & Line -->
                                    <div class="flex flex-col items-center">
                                        <div class="w-6 h-6 rounded-full bg-[#1E3A8A] border-4 border-blue-50 flex items-center justify-center shrink-0 z-10">
                                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                        </div>
                                        @if (!$isLast)
                                            <div class="w-0.5 bg-[#1E3A8A]/30 h-full min-h-[40px] absolute top-6 bottom-0 left-[11px]"></div>
                                        @endif
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="pb-8">
                                        <p class="font-semibold text-[13px] text-navy-dark">
                                            {{ ucwords(str_replace('_', ' ', $riwayat->status)) }}
                                        </p>
                                        <p class="text-[11px] text-gray-400 mt-1">
                                            {{ $riwayat->keterangan ?? 'Tercatat oleh sistem' }} • {{ $riwayat->created_at?->format('d M Y H:i') ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400">Belum ada riwayat aktivitas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- COLUMN RIGHT: Verifikasi & Konsultasi Panel (lg:col-span-4) -->
            <div class="lg:col-span-4 space-y-6">
                
                <!-- Card Hasil Verifikasi Berkas (dari Staf Legal) -->
                @if ($verifikasiTerakhir)
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm space-y-4">
                        <div class="border-b border-[#F1F5F9] pb-3">
                            <h4 class="font-bold text-navy-dark text-base">Hasil Verifikasi Berkas</h4>
                            <p class="text-xs text-gray-400 mt-1">Catatan pemeriksaan oleh Staf Legal</p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Periksa</span>
                                <span class="block text-xs font-semibold text-navy-dark mt-0.5">{{ $verifikasiTerakhir->tanggal_verifikasi?->format('d M Y • H:i') ?? '-' }} WIB</span>
                            </div>
                            <div>
                                <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Pemeriksa</span>
                                <span class="block text-xs font-semibold text-navy-dark mt-0.5">{{ $verifikasiTerakhir->user?->nama ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Catatan Umum</span>
                                <p class="text-xs text-gray-600 bg-gray-50/50 p-3 rounded-xl border border-gray-100 mt-1 leading-relaxed whitespace-pre-line italic">
                                    "{{ $verifikasiTerakhir->catatan_verifikasi_umum ?? 'Berkas diperiksa.' }}"
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Card Informasi Pertemuan Konsultasi -->
                @if ($bookingTampil || $praPendaftaranPerkara->status_pengajuan === 'berkas_lengkap' || $praPendaftaranPerkara->status_pengajuan === 'selesai')
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm space-y-4">
                        <div class="border-b border-[#F1F5F9] pb-3 flex items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-navy-dark text-base">Informasi Konsultasi</h4>
                                <p class="text-xs text-gray-400 mt-1">Status pertemuan konsultasi Anda</p>
                            </div>
                        </div>

                        <!-- CTA Pilih Jadwal (jika berkas lengkap dan belum ada booking) -->
                        @if (!$bookingAktif && $praPendaftaranPerkara->status_pengajuan === 'berkas_lengkap')
                            <div class="py-2 text-center space-y-3">
                                <p class="text-xs text-gray-500 leading-relaxed">Berkas perkara Anda sudah dinyatakan lengkap oleh verifikator. Silakan pilih slot jadwal konsultasi yang tersedia.</p>
                                <a href="{{ route('klien.booking-konsultasi.create', $praPendaftaranPerkara) }}" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm py-3 rounded-xl w-full block text-center transition shadow-md shadow-blue-900/20">
                                    Pilih Jadwal Konsultasi
                                </a>
                            </div>
                        @endif

                        @if ($bookingTampil)
                            @php
                                $jadwalBooking = $bookingTampil->jadwalKonsultasi;
                                $metodeBooking = $bookingTampil->metode_konsultasi ?? 'offline';
                                $statusKonfirmasi = $bookingTampil->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi';
                            @endphp

                            <!-- Status Badges -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <x-status-badge :status="$bookingTampil->status_booking" />
                                <x-status-badge :status="$statusKonfirmasi" />
                            </div>

                            <!-- Reschedule status alert -->
                            @if ($permintaanRescheduleTerakhir)
                                <div class="p-3 rounded-xl text-xs leading-relaxed border mt-2 {{ $permintaanRescheduleTerakhir->status_reschedule === 'menunggu_persetujuan' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : ($permintaanRescheduleTerakhir->status_reschedule === 'disetujui' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                                    <strong>Status Reschedule:</strong>
                                    <div class="flex items-center justify-between gap-3 mt-1.5">
                                        <x-status-badge :status="$permintaanRescheduleTerakhir->status_reschedule" />
                                        <a href="{{ route('klien.permintaan-reschedule.show', $permintaanRescheduleTerakhir) }}" class="font-bold underline">Detail &rarr;</a>
                                    </div>
                                    @if ($permintaanRescheduleTerakhir->status_reschedule === 'menunggu_persetujuan')
                                        <p class="mt-2 text-[10px] text-gray-500 italic">Jadwal lama di bawah ini tetap berlaku sampai Admin menyetujui permintaan reschedule.</p>
                                    @endif
                                </div>
                            @endif

                            <!-- Slot info box -->
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl space-y-2">
                                <div class="flex items-center gap-2 text-xs font-semibold text-[#334155]">
                                    <svg class="h-4 w-4 text-accent-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>{{ $jadwalBooking?->tanggal?->format('l, d M Y') ?? '-' }}</span>
                                </div>
                                <div class="pl-6 text-xxs text-gray-500 font-medium">
                                    {{ $jadwalBooking ? $jadwalBooking->waktu_mulai . ' – ' . $jadwalBooking->waktu_selesai . ' WIB' : '-' }}
                                </div>
                            </div>

                            <!-- Case info & Method -->
                            <div class="space-y-3 pt-2 text-xs">
                                <div class="flex items-center gap-2">
                                    <x-status-badge :status="$metodeBooking" />
                                </div>

                                @if ($statusKonfirmasi === 'terkonfirmasi')
                                    <div class="bg-green-50/50 border border-green-100 p-3 rounded-xl text-green-700 leading-relaxed">
                                        <strong>Lokasi / Link Pertemuan:</strong><br>
                                        @if ($metodeBooking === 'online')
                                            <a href="{{ $bookingTampil->link_konsultasi }}" target="_blank" class="text-accent-blue hover:underline break-all font-semibold">
                                                {{ $bookingTampil->link_konsultasi }}
                                            </a>
                                        @else
                                            <span class="font-medium text-gray-700">{{ $bookingTampil->lokasi_konsultasi ?? '-' }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="bg-yellow-50/50 border border-yellow-100 p-3 rounded-xl text-yellow-800 leading-relaxed">
                                        Menunggu Admin melengkapi detail alamat atau link pertemuan online.
                                    </div>
                                @endif

                                @if ($bookingTampil->catatan_preferensi_klien)
                                    <div>
                                        <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Catatan Preferensi Klien</span>
                                        <span class="block font-medium text-gray-600 mt-0.5 italic">"{{ $bookingTampil->catatan_preferensi_klien }}"</span>
                                    </div>
                                @endif

                                @if ($bookingTampil->catatan_konsultasi)
                                    <div>
                                        <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Catatan Konsultasi Admin</span>
                                        <span class="block font-medium text-gray-600 mt-0.5 italic">"{{ $bookingTampil->catatan_konsultasi }}"</span>
                                    </div>
                                @endif
                            </div>

                            <!-- CTA Reschedule (jika diperbolehkan) -->
                            @if ($bisaAjukanReschedule)
                                <div class="pt-4 border-t border-[#F1F5F9]">
                                    <a href="{{ route('klien.permintaan-reschedule.create', $bookingAktif) }}" class="bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue text-center font-bold py-2.5 rounded-xl text-xs transition flex items-center justify-center gap-2 shadow-sm w-full">
                                        <svg class="h-4 w-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H12v9l-7-7"></path>
                                        </svg>
                                        <span>Ajukan Reschedule Jadwal</span>
                                    </a>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
