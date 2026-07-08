<x-app-layout title="Detail Booking Konsultasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Booking Konsultasi', 'url' => route('admin.booking-konsultasi.index')], ['label' => 'BK-' . str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT)]]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <a href="{{ route('admin.booking-konsultasi.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </a>
        </div>

    @php
        $pengajuan = $bookingKonsultasi->praPendaftaranPerkara;
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
        $metode = $bookingKonsultasi->metode_konsultasi ?? 'offline';
        $statusKonfirmasi = $bookingKonsultasi->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi';
        
        $permintaanRescheduleMenunggu = $bookingKonsultasi->permintaanReschedule
            ->firstWhere('status_reschedule', 'menunggu_persetujuan');
            
        $canConfirm = $bookingKonsultasi->status_booking === 'aktif'
            && $pengajuan?->status_pengajuan === 'jadwal_dipilih';
            
        $canComplete = $bookingKonsultasi->status_booking === 'aktif'
            && $pengajuan?->status_pengajuan === 'jadwal_dipilih'
            && $statusKonfirmasi === 'terkonfirmasi'
            && !$permintaanRescheduleMenunggu;
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-3">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-3">
                <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-xs font-semibold space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600 shrink-0"></span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Grid Layout for Booking Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Left Card: Klien & Pengajuan Info -->
            <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Informasi Klien & Perkara</h3>
                    <p class="text-xs text-gray-400 mt-1">Detail pemohon konsultasi dan perkara terkait.</p>
                </div>

                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0 flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $bookingKonsultasi->klien?->nama ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $bookingKonsultasi->klien?->email ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Nomor Telepon</span>
                        <span class="text-sm font-semibold text-navy-dark font-mono">{{ $bookingKonsultasi->klien?->no_telepon ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Judul Perkara</span>
                        <span class="text-sm font-semibold text-navy-dark max-w-xs text-right">{{ $pengajuan?->judul_perkara ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Kategori Perkara</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Right Card: Jadwal & Status Info -->
            <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Jadwal & Status Konsultasi</h3>
                    <p class="text-xs text-gray-400 mt-1">Detail slot waktu dan status booking.</p>
                </div>

                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0 flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Konsultasi</span>
                        <span class="text-sm font-bold text-navy-dark">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Waktu Konsultasi</span>
                        <span class="text-sm font-semibold text-navy-dark font-mono">
                            {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) : '-' }}
                            @if ($jadwal)
                                - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                            @endif
                        </span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Metode Konsultasi</span>
                        <x-status-badge :status="$metode" />
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Booking</span>
                        <x-status-badge :status="$bookingKonsultasi->status_booking" />
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Konfirmasi</span>
                        <x-status-badge :status="$statusKonfirmasi" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Consultation Details (Link / Lokasi / Catatan) -->
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-navy-dark text-lg">Detail Teknis Pelaksanaan</h3>
                <p class="text-xs text-gray-400 mt-1">Link atau lokasi pelaksanaan konsultasi yang telah dikonfirmasi.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-[#E2E8F0]">
                <!-- Link atau Lokasi -->
                <div class="space-y-4">
                    @if ($metode === 'online')
                        <div>
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Link Konsultasi Online</span>
                            <div class="mt-2">
                                @if ($bookingKonsultasi->link_konsultasi)
                                    <a href="{{ $bookingKonsultasi->link_konsultasi }}" class="text-sm font-semibold text-[#1D4ED8] hover:underline break-all inline-flex items-center gap-1.5" target="_blank" rel="noopener noreferrer">
                                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        <span>{{ $bookingKonsultasi->link_konsultasi }}</span>
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500 font-semibold italic">{{ __('Belum tersedia (belum dikonfirmasi)') }}</span>
                                @endif
                            </div>
                        </div>
                    @else
                        <div>
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Lokasi Konsultasi Offline</span>
                            <div class="mt-2 text-sm font-semibold text-navy-dark leading-relaxed">
                                {{ $bookingKonsultasi->lokasi_konsultasi ?: __('Belum tersedia (belum dikonfirmasi)') }}
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-[#E2E8F0] md:border-t-0 md:pt-0">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Preferensi Klien</span>
                        <div class="mt-2 text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl whitespace-pre-line">
                            {{ $bookingKonsultasi->catatan_preferensi_klien ?: 'Klien tidak menyertakan preferensi khusus.' }}
                        </div>
                    </div>
                </div>

                <!-- Catatan & Admin Info -->
                <div class="space-y-4 md:pl-6 pt-6 md:pt-0">
                    <div>
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Catatan Konsultasi (Admin)</span>
                        <div class="mt-2 text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl whitespace-pre-line">
                            {{ $bookingKonsultasi->catatan_konsultasi ?: 'Tidak ada catatan tambahan untuk pelaksanaan konsultasi.' }}
                        </div>
                    </div>

                    @if ($statusKonfirmasi === 'terkonfirmasi')
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-[#E2E8F0]">
                            <div>
                                <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Admin Konfirmasi</span>
                                <div class="text-xs font-semibold text-navy-dark mt-1">{{ $bookingKonsultasi->adminKonfirmasi?->nama ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Waktu Konfirmasi</span>
                                <div class="text-xs font-semibold text-navy-dark mt-1">{{ $bookingKonsultasi->dikonfirmasi_pada?->format('d M Y H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Action Card 1: Konfirmasi Detail Konsultasi (Form) -->
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-navy-dark text-lg">Konfirmasi Detail Pelaksanaan</h3>
                <p class="text-xs text-gray-400 mt-1">Lengkapi informasi akses agar klien dapat melangsungkan konsultasi.</p>
            </div>

            @if ($canConfirm)
                <form method="POST" action="{{ route('admin.booking-konsultasi.konfirmasi', $bookingKonsultasi) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    @if ($metode === 'online')
                        <div>
                            <label for="link_konsultasi" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Link Video Call (Google Meet, Zoom, dll)</label>
                            <input id="link_konsultasi" name="link_konsultasi" type="url" value="{{ old('link_konsultasi', $bookingKonsultasi->link_konsultasi) }}" required placeholder="https://meet.google.com/abc-defg-hij"
                                class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                            @if($errors->has('link_konsultasi'))
                                <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('link_konsultasi') }}</div>
                            @endif
                        </div>
                    @else
                        <div>
                            <label for="lokasi_konsultasi" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Lokasi / Ruang Pertemuan</label>
                            <input id="lokasi_konsultasi" name="lokasi_konsultasi" type="text" value="{{ old('lokasi_konsultasi', $bookingKonsultasi->lokasi_konsultasi) }}" required placeholder="Ruang Rapat Utama, Kantor TNY Law Firm Lantai 2"
                                class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                            @if($errors->has('lokasi_konsultasi'))
                                <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('lokasi_konsultasi') }}</div>
                            @endif
                        </div>
                    @endif

                    <div>
                        <label for="catatan_konsultasi" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Catatan Tambahan untuk Klien</label>
                        <textarea id="catatan_konsultasi" name="catatan_konsultasi" rows="4" placeholder="Tulis instruksi tambahan, misalnya: 'Harap hadir 5 menit sebelum jadwal.'"
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm p-4">{{ old('catatan_konsultasi', $bookingKonsultasi->catatan_konsultasi) }}</textarea>
                        @if($errors->has('catatan_konsultasi'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('catatan_konsultasi') }}</div>
                        @endif
                    </div>

                    <div class="flex justify-end pt-4 border-t border-[#E2E8F0]">
                        <button type="submit" class="inline-flex items-center justify-center bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest">
                            {{ $statusKonfirmasi === 'terkonfirmasi' ? __('Perbarui Konfirmasi') : __('Konfirmasi Sekarang') }}
                        </button>
                    </div>
                </form>
            @else
                <div class="bg-gray-50 border border-gray-200 text-gray-600 rounded-xl p-4 text-xs font-semibold flex items-center gap-3">
                    <svg class="h-4 w-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Booking ini tidak dapat dikonfirmasi karena status booking tidak aktif atau pengajuan tidak berstatus 'jadwal_dipilih'.</span>
                </div>
            @endif
        </div>

        <!-- Action Card 2: Selesaikan Konsultasi -->
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-navy-dark text-lg">Penyelesaian Konsultasi</h3>
                <p class="text-xs text-gray-400 mt-1">Tandai konsultasi sebagai selesai setelah sesi pertemuan berakhir.</p>
            </div>

            @if ($canComplete)
                <div class="space-y-4">
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Pastikan konsultasi telah terlaksana dengan baik bersama klien. Setelah ditandai selesai, status booking dan pengajuan perkara terkait akan diperbarui menjadi **Selesai**. Tindakan ini tidak dapat dibatalkan.
                    </p>

                    <form method="POST" action="{{ route('admin.booking-konsultasi.selesai', $bookingKonsultasi) }}" class="flex justify-end pt-4 border-t border-[#E2E8F0]">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-emerald-700/20 uppercase tracking-widest">
                            {{ __('Tandai Konsultasi Selesai') }}
                        </button>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 border border-gray-200 text-gray-600 rounded-xl p-4 text-xs font-semibold flex items-center gap-3">
                    <svg class="h-4 w-4 text-gray-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>
                        @if ($bookingKonsultasi->status_booking === 'selesai')
                            Konsultasi ini sudah ditandai selesai.
                        @elseif ($bookingKonsultasi->status_booking === 'dibatalkan')
                            Booking ini sudah dibatalkan.
                        @elseif ($statusKonfirmasi !== 'terkonfirmasi')
                            Harap konfirmasi detail teknis pelaksanaan terlebih dahulu sebelum menyelesaikan.
                        @elseif ($permintaanRescheduleMenunggu)
                            Menunggu penyelesaian permintaan reschedule yang diajukan oleh klien.
                        @else
                            Konsultasi belum dapat diselesaikan saat ini.
                        @endif
                    </span>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
