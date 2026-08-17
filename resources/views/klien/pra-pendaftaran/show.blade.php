<x-app-layout title="Detail Pengajuan Perkara" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Pengajuan', 'url' => route('klien.pra-pendaftaran.index')], ['label' => 'PP-' . str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT)]]">

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
        
        $isBerkasLengkap = $praPendaftaranPerkara->status_pengajuan === 'berkas_lengkap';
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

        <!-- 1. HEADER CARD -->
        <x-card class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="bg-blue-50 text-accent-blue font-bold font-mono text-xs px-3 py-1 rounded-lg">
                        PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                    </span>
                    <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                </div>
                <div>
                    <h3 class="font-extrabold text-navy-dark text-2xl leading-tight">
                        {{ $praPendaftaranPerkara->judul_perkara }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-2">
                        @if ($isBerkasLengkap && !$bookingAktif)
                            Berkas sudah lengkap dan Klien dapat memilih jadwal konsultasi.
                        @elseif ($bookingAktif)
                            Klien telah memiliki jadwal konsultasi.
                        @else
                            Berkas belum lengkap atau sedang dalam proses verifikasi.
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="shrink-0 flex items-center">
                @if ($isBerkasLengkap && !$bookingAktif)
                    <!-- Enabled Pilih Jadwal -->
                    <a href="{{ route('klien.booking-konsultasi.create', $praPendaftaranPerkara) }}" class="bg-navy-primary hover:bg-navy-dark text-white font-bold text-sm px-6 py-3 rounded-xl transition shadow-md shadow-blue-900/20 inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Pilih Jadwal
                    </a>
                @elseif (!$bookingTampil)
                    <!-- Disabled Pilih Jadwal -->
                    <button type="button" disabled class="bg-gray-100 text-gray-400 font-bold text-sm px-6 py-3 rounded-xl cursor-not-allowed inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Pilih Jadwal
                    </button>
                @endif
            </div>
        </x-card>

        <!-- 2. INFORMASI & KRONOLOGI GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
            
            <!-- Informasi Pengajuan -->
            <x-card class="flex flex-col justify-start">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg mb-6">Informasi Pengajuan</h3>
                    <div class="space-y-0">
                        <div class="grid grid-cols-3 py-4 border-b border-[#F1F5F9]">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider col-span-1 flex items-center">Kategori</span>
                            <span class="text-sm font-semibold text-navy-dark col-span-2">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-3 py-4 border-b border-[#F1F5F9]">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider col-span-1 flex items-center">Judul</span>
                            <span class="text-sm font-semibold text-navy-dark col-span-2">{{ $praPendaftaranPerkara->judul_perkara }}</span>
                        </div>
                        <div class="grid grid-cols-3 py-4 border-b border-[#F1F5F9]">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider col-span-1 flex items-center">Tanggal Pengajuan</span>
                            <span class="text-sm font-semibold text-navy-dark col-span-2">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                        </div>
                        <div class="grid grid-cols-3 py-4 border-b border-[#F1F5F9]">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider col-span-1 flex items-center">Status</span>
                            <span class="text-sm font-semibold text-navy-dark col-span-2">{{ ucwords(str_replace('_', ' ', $praPendaftaranPerkara->status_pengajuan)) }}</span>
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Kronologi Perkara -->
            <x-card class="flex flex-col">
                <h3 class="font-bold text-navy-dark text-lg mb-6">Kronologi Perkara</h3>
                <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ $praPendaftaranPerkara->kronologi }}
                </p>
            </x-card>
            
        </div>

        <!-- 3. INFORMASI KONSULTASI (Jika ada) -->
        @if ($bookingTampil)
            @php
                $jadwalBooking = $bookingTampil->jadwalKonsultasi;
                $metodeBooking = $bookingTampil->metode_konsultasi ?? 'offline';
                $statusKonfirmasi = $bookingTampil->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi';
            @endphp
            <x-card class="space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-navy-dark text-lg">Informasi Konsultasi</h3>
                        <p class="text-xs text-gray-500 mt-1">Status dan detail jadwal pertemuan konsultasi Anda.</p>
                    </div>
                    <div class="flex gap-2">
                        <x-status-badge :status="$bookingTampil->status_booking" />
                        <x-status-badge :status="$statusKonfirmasi" />
                    </div>
                </div>

                <!-- Reschedule status alert -->
                @if ($permintaanRescheduleTerakhir)
                    <div class="p-4 rounded-xl text-sm leading-relaxed border mt-2 {{ $permintaanRescheduleTerakhir->status_reschedule === 'menunggu_persetujuan' ? 'bg-yellow-50 border-yellow-200 text-yellow-800' : ($permintaanRescheduleTerakhir->status_reschedule === 'disetujui' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800') }}">
                        <strong>Status Reschedule:</strong>
                        <div class="flex items-center justify-between gap-3 mt-1.5">
                            <x-status-badge :status="$permintaanRescheduleTerakhir->status_reschedule" />
                            <a href="{{ route('klien.permintaan-reschedule.show', $permintaanRescheduleTerakhir) }}" class="font-bold underline">Detail Reschedule &rarr;</a>
                        </div>
                        @if ($permintaanRescheduleTerakhir->status_reschedule === 'menunggu_persetujuan')
                            <p class="mt-2 text-xs text-gray-500 italic">Jadwal lama di bawah ini tetap berlaku sampai Admin menyetujui permintaan reschedule.</p>
                        @endif
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Slot info box -->
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-5 rounded-xl space-y-3">
                        <div class="flex items-center gap-3 text-sm font-semibold text-navy-dark">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                <svg class="h-4 w-4 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <span>{{ $jadwalBooking?->tanggal?->format('l, d M Y') ?? '-' }}</span>
                        </div>
                        <div class="pl-11 text-sm text-gray-600 font-medium">
                            {{ $jadwalBooking ? $jadwalBooking->waktu_mulai . ' – ' . $jadwalBooking->waktu_selesai . ' WIB' : '-' }}
                        </div>
                    </div>

                    <!-- Location info -->
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                            <x-status-badge :status="$metodeBooking" />
                        </div>
                        @if ($statusKonfirmasi === 'terkonfirmasi')
                            <div class="bg-green-50/50 border border-green-100 p-4 rounded-xl text-green-700 leading-relaxed text-sm">
                                <strong>Lokasi / Link Pertemuan:</strong><br>
                                @if ($metodeBooking === 'online')
                                    <a href="{{ $bookingTampil->link_konsultasi }}" target="_blank" class="text-accent-blue hover:underline break-all font-semibold mt-1 inline-block">
                                        {{ $bookingTampil->link_konsultasi }}
                                    </a>
                                @else
                                    <span class="font-medium text-gray-700 mt-1 inline-block">{{ $bookingTampil->lokasi_konsultasi ?? '-' }}</span>
                                @endif
                            </div>
                        @else
                            <div class="bg-yellow-50/50 border border-yellow-100 p-4 rounded-xl text-yellow-800 leading-relaxed text-sm">
                                Menunggu Admin melengkapi detail alamat atau link pertemuan online.
                            </div>
                        @endif
                    </div>
                </div>

                @if ($bookingTampil->catatan_preferensi_klien || $bookingTampil->catatan_konsultasi)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-[#F1F5F9]">
                        @if ($bookingTampil->catatan_preferensi_klien)
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan Preferensi Klien</span>
                                <span class="block text-sm font-medium text-gray-600 mt-2 italic">"{{ $bookingTampil->catatan_preferensi_klien }}"</span>
                            </div>
                        @endif
                        @if ($bookingTampil->catatan_konsultasi)
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan Konsultasi Admin</span>
                                <span class="block text-sm font-medium text-gray-600 mt-2 italic">"{{ $bookingTampil->catatan_konsultasi }}"</span>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($bisaAjukanReschedule)
                    <div class="pt-4 border-t border-[#F1F5F9] flex justify-end">
                        <x-secondary-button href="{{ route('klien.permintaan-reschedule.create', $bookingAktif) }}" tag="a" class="justify-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Ajukan Reschedule Jadwal</span>
                        </x-secondary-button>
                    </div>
                @endif
            </div>
        @endif

        <!-- 4. CATATAN PERBAIKAN DOKUMEN (Jika berkas_tidak_lengkap) -->
        @if ($praPendaftaranPerkara->status_pengajuan === 'berkas_tidak_lengkap')
            @php
                $catatanPerbaikan = $praPendaftaranPerkara->verifikasiBerkas
                    ->flatMap(fn ($verifikasi) => $verifikasi->catatanVerifikasi);
            @endphp
            <x-card class="p-0 overflow-hidden sm:p-0 border-red-100">
                <div class="p-6 border-b border-red-50 bg-red-50/30">
                    <h4 class="font-bold text-red-900 text-lg">Catatan Perbaikan Dokumen</h4>
                    <p class="text-sm text-red-700 mt-1">Harap perbaiki dokumen bermasalah di bawah ini sesuai instruksi verifikator Staf Legal.</p>
                </div>

                <!-- Desktop Table Layout -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#F1F5F9]">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Instruksi Catatan</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status Koreksi</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
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
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <x-empty-state title="Tidak Ada Catatan" message="Tidak ada catatan perbaikan dokumen spesifik." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Layout -->
                <div class="block md:hidden divide-y divide-[#F1F5F9] bg-white">
                    @forelse ($catatanPerbaikan as $catatan)
                        @php
                            $dokumenCatatan = $catatan->dokumenPerkara;
                            $bisaUploadPerbaikan = $catatan->status_perbaikan === 'belum_diperbaiki'
                                && $dokumenCatatan
                                && $dokumenCatatan->status_dokumen === 'perlu_perbaikan';
                        @endphp
                        <div class="p-4 space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-semibold text-navy-dark">{{ $dokumenCatatan?->nama_dokumen ?? '-' }}</span>
                                <x-status-badge :status="$catatan->status_perbaikan" />
                            </div>
                            <div class="text-xs text-gray-500 font-medium">
                                Jenis: {{ $dokumenCatatan?->jenis_dokumen ?? '-' }}
                            </div>
                            <div class="text-xs text-gray-600 bg-red-50/50 p-2.5 rounded-lg border border-red-100/50 leading-relaxed whitespace-pre-line">
                                <strong>Instruksi Catatan:</strong> {{ $catatan->isi_catatan }}
                            </div>
                            <div class="flex justify-end pt-2 border-t border-gray-100">
                                @if ($bisaUploadPerbaikan)
                                    <a href="{{ route('klien.perbaikan-dokumen.create', $catatan) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline">
                                        <span>Unggah Ulang</span>
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-gray-400 font-medium text-xs">Selesai diperbaiki</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-6">
                            <x-empty-state title="Tidak Ada Catatan" message="Tidak ada catatan perbaikan dokumen spesifik." />
                        </div>
                    @endforelse
                </div>
            </x-card>
        @endif

        <!-- 5. DOKUMEN PENDUKUNG -->
        <x-card class="p-0 overflow-hidden sm:p-0">
            <div class="p-6 sm:p-8 border-b border-[#F1F5F9] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Dokumen Pendukung</h3>
                    <p class="text-sm text-gray-500 mt-1">Dokumen yang telah diunggah pada pengajuan ini.</p>
                </div>
                @if ($praPendaftaranPerkara->status_pengajuan === 'menunggu_verifikasi')
                    <x-primary-button href="{{ route('klien.dokumen.create', $praPendaftaranPerkara) }}" tag="a" class="gap-2">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        Upload Dokumen Baru
                    </x-primary-button>
                @endif
            </div>

            <!-- Desktop Table Layout -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F1F5F9]">
                    <thead class="bg-white">
                        <tr>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-[#F1F5F9]">Nama</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-[#F1F5F9]">Jenis</th>
                            <th class="px-8 py-5 text-left text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-[#F1F5F9]">Status</th>
                            <th class="px-8 py-5 text-right text-xs font-bold text-gray-500 uppercase tracking-widest border-b border-[#F1F5F9]">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#F1F5F9]">
                        @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-8 py-5 whitespace-nowrap text-sm font-semibold text-navy-dark">
                                    {{ $dokumen->nama_dokumen }}
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-xs text-gray-500 font-medium">
                                    <span class="bg-gray-100 px-3 py-1 rounded-md">{{ $dokumen->jenis_dokumen }}</span>
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap">
                                    <x-status-badge :status="$dokumen->status_dokumen" />
                                </td>
                                <td class="px-8 py-5 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('klien.dokumen.show', $dokumen) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-accent-blue hover:text-blue-800 transition">
                                        <span>Lihat Dokumen</span>
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002-2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <x-empty-state title="Tidak Ada Dokumen" message="Belum ada dokumen aktif yang diunggah." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout -->
            <div class="block md:hidden divide-y divide-[#F1F5F9] bg-white">
                @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                    <div class="p-6 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-navy-dark">{{ $dokumen->nama_dokumen }}</span>
                            <x-status-badge :status="$dokumen->status_dokumen" />
                        </div>
                        <div class="text-xs text-gray-500 font-medium flex gap-2 items-center">
                            <span class="bg-gray-100 px-2 py-1 rounded-md">{{ $dokumen->jenis_dokumen }}</span>
                            <span class="text-gray-400">{{ $dokumen->created_at?->format('d M Y') ?? '-' }}</span>
                        </div>
                        <div class="flex justify-end pt-3 mt-2 border-t border-gray-50">
                            <a href="{{ route('klien.dokumen.show', $dokumen) }}" class="inline-flex items-center gap-1.5 text-sm font-bold text-accent-blue hover:text-blue-800 transition">
                                <span>Lihat Dokumen</span>
                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002-2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8">
                        <x-empty-state title="Tidak Ada Dokumen" message="Belum ada dokumen aktif yang diunggah." />
                    </div>
                @endforelse
            </div>
        </x-card>

        <!-- 6. RIWAYAT BAWAH (Timeline, Verifikasi, Histori) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
            <!-- Timeline Riwayat Status -->
            <x-card class="p-0 overflow-hidden sm:p-0">
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
                                    <div class="w-6 h-6 rounded-full bg-navy-primary border-4 border-blue-50 flex items-center justify-center shrink-0 z-10">
                                        <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                                    </div>
                                    @if (!$isLast)
                                        <div class="w-0.5 bg-navy-primary/30 h-full min-h-[40px] absolute top-6 bottom-0 left-[11px]"></div>
                                    @endif
                                </div>
                                
                                <!-- Content -->
                                <div class="pb-8">
                                    <p class="font-semibold text-sm text-navy-dark">
                                        {{ ucwords(str_replace('_', ' ', $riwayat->status)) }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-1">
                                        {{ $riwayat->keterangan ?? 'Tercatat oleh sistem' }} • {{ $riwayat->created_at?->format('d M Y H:i') ?? '-' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Belum ada riwayat aktivitas.</p>
                        @endforelse
                    </div>
                </div>
            </x-card>

            <div class="space-y-6">
                <!-- Hasil Verifikasi Berkas -->
                @if ($verifikasiTerakhir)
                    <x-card class="space-y-4">
                        <div class="border-b border-[#F1F5F9] pb-3">
                            <h4 class="font-bold text-navy-dark text-base">Hasil Verifikasi Terakhir</h4>
                            <p class="text-xs text-gray-400 mt-1">Catatan pemeriksaan oleh Staf Legal</p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Periksa</span>
                                <span class="block text-xs font-semibold text-navy-dark mt-0.5">{{ $verifikasiTerakhir->tanggal_verifikasi?->format('d M Y • H:i') ?? '-' }} WIB</span>
                            </div>
                            <div>
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Catatan Umum</span>
                                <p class="text-xs text-gray-600 bg-gray-50/50 p-3 rounded-xl border border-gray-100 mt-1 leading-relaxed whitespace-pre-line italic">
                                    "{{ $verifikasiTerakhir->catatan_verifikasi_umum ?? 'Berkas diperiksa.' }}"
                                </p>
                            </div>
                        </div>
                    </x-card>
                @endif

                <!-- Histori Dokumen Lama -->
                @if ($praPendaftaranPerkara->riwayatDokumen->isNotEmpty())
                    <x-card class="p-0 overflow-hidden sm:p-0">
                        <div class="p-6 border-b border-[#F1F5F9]">
                            <h4 class="font-bold text-navy-dark text-base">Histori Dokumen Lama</h4>
                            <p class="text-xs text-gray-400 mt-1">Arsip dokumen sebelum perbaikan</p>
                        </div>
                        <div class="divide-y divide-[#F1F5F9]">
                            @foreach ($praPendaftaranPerkara->riwayatDokumen as $riwayatDok)
                                <div class="p-4 flex justify-between items-center hover:bg-gray-50/50 transition">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-600">{{ $riwayatDok->nama_dokumen }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5">{{ $riwayatDok->created_at?->format('d M Y') ?? '-' }}</p>
                                    </div>
                                    <a href="{{ route('klien.dokumen.show', $riwayatDok) }}" class="text-xs font-semibold text-accent-blue hover:underline">
                                        Lihat
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
