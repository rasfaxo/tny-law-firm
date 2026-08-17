<x-app-layout title="Detail Booking Konsultasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Booking Konsultasi', 'url' => route('admin.booking-konsultasi.index')], ['label' => 'BK-' . str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT)]]">

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

    <div x-data="{ showForm: {{ $errors->any() ? 'true' : 'false' }} }">
        
        <!-- ============================================== -->
        <!-- VIEW 1: DETAIL BOOKING (FIGMA NODE 84-2982)    -->
        <!-- ============================================== -->
        <div x-show="!showForm" class="space-y-5">
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

            <!-- 1. Hero Card: Title, Status Badges, & Actions -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-2">
                    <h2 class="text-xl font-bold text-navy-dark leading-snug">
                        BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }} — {{ $pengajuan?->judul_perkara ?? 'Konsultasi Perkara' }}
                    </h2>
                    <p class="text-sm text-gray-500 leading-normal">
                        @if ($bookingKonsultasi->status_booking === 'selesai')
                            Booking konsultasi ini telah selesai dilaksanakan.
                        @elseif ($bookingKonsultasi->status_booking === 'dibatalkan')
                            Booking konsultasi ini telah dibatalkan.
                        @elseif ($statusKonfirmasi === 'terkonfirmasi')
                            Booking aktif dan telah dikonfirmasi. Siap untuk pelaksanaan konsultasi.
                        @else
                            Booking aktif dengan status konfirmasi masih menunggu. Admin perlu melengkapi detail teknis konsultasi.
                        @endif
                    </p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <x-status-badge :status="$bookingKonsultasi->status_booking" />
                        <x-status-badge :status="$statusKonfirmasi" />
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2.5 shrink-0 self-start md:self-center">
                    @if ($canConfirm)
                        <x-primary-button @click="showForm = true" type="button">
                            <span>{{ $statusKonfirmasi === 'terkonfirmasi' ? __('Perbarui Konfirmasi') : __('Konfirmasi Konsultasi') }}</span>
                        </x-primary-button>
                    @endif

                    @if ($canComplete)
                        <form method="POST" action="{{ route('admin.booking-konsultasi.selesai', $bookingKonsultasi) }}">
                            @csrf
                            @method('PATCH')
                            <x-secondary-button type="submit" onclick="return confirm('Apakah Anda yakin sesi konsultasi telah selesai? Status tidak dapat dikembalikan.')">
                                {{ __('Tandai Selesai') }}
                            </x-secondary-button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- 2. Middle 3-Column Info Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Card 1: Informasi Klien -->
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="border-b border-[#F1F5F9] pb-3 mb-4">
                            <h3 class="text-sm font-bold text-navy-dark">Informasi Klien</h3>
                        </div>
                        <div class="space-y-3 divide-y divide-[#E2E8F0]">
                            <div class="pt-0">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Nama</span>
                                <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $bookingKonsultasi->klien?->nama ?? '-' }}</p>
                            </div>
                            <div class="pt-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Email</span>
                                <p class="text-sm font-semibold text-navy-dark mt-0.5 break-all">{{ $bookingKonsultasi->klien?->email ?? '-' }}</p>
                            </div>
                            <div class="pt-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Nomor Telepon</span>
                                <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $bookingKonsultasi->klien?->no_telepon ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Informasi Pengajuan -->
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="border-b border-[#F1F5F9] pb-3 mb-4">
                            <h3 class="text-sm font-bold text-navy-dark">Informasi Pengajuan</h3>
                        </div>
                        <div class="space-y-3 divide-y divide-[#E2E8F0]">
                            <div class="pt-0">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Kode Pengajuan</span>
                                <p class="text-sm font-semibold font-mono text-accent-blue mt-0.5">
                                    {{ $pengajuan ? 'PP-' . str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) : '-' }}
                                </p>
                            </div>
                            <div class="pt-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Kategori Perkara</span>
                                <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</p>
                            </div>
                            <div class="pt-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Status Pengajuan</span>
                                @if ($pengajuan?->status_pengajuan)
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Jadwal dan Metode -->
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="border-b border-[#F1F5F9] pb-3 mb-4">
                            <h3 class="text-sm font-bold text-navy-dark">Jadwal dan Metode</h3>
                        </div>
                        <div class="space-y-3 divide-y divide-[#E2E8F0]">
                            <div class="pt-0">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Jadwal</span>
                                <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) : '-' }}
                                    @if ($jadwal)
                                        – {{ substr((string) $jadwal->waktu_selesai, 0, 5) }} WIB
                                    @endif
                                </p>
                            </div>
                            <div class="pt-3">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Metode</span>
                                <x-status-badge :status="$metode" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Bottom Card: Informasi Konsultasi -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm space-y-6">
                <div class="border-b border-[#F1F5F9] pb-4">
                    <h3 class="text-lg font-bold text-navy-dark">Informasi Konsultasi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-5">
                    <!-- Left Column -->
                    <div class="space-y-4 divide-y divide-[#E2E8F0]">
                        <div class="pt-0">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Status Booking</span>
                            <x-status-badge :status="$bookingKonsultasi->status_booking" />
                        </div>
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Status Konfirmasi</span>
                            <x-status-badge :status="$statusKonfirmasi" />
                        </div>
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Catatan Preferensi Klien</span>
                            <p class="text-sm text-navy-dark mt-1 whitespace-pre-line leading-relaxed">
                                {{ $bookingKonsultasi->catatan_preferensi_klien ?: '–' }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            @if ($metode === 'online')
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Link Konsultasi</span>
                                <div class="mt-1">
                                    @if ($bookingKonsultasi->link_konsultasi)
                                        <a href="{{ $bookingKonsultasi->link_konsultasi }}" class="text-sm font-semibold text-accent-blue hover:underline break-all inline-flex items-center gap-1.5" target="_blank" rel="noopener noreferrer">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            <span>{{ $bookingKonsultasi->link_konsultasi }}</span>
                                        </a>
                                    @else
                                        <span class="text-sm font-semibold italic text-gray-400">Belum tersedia</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Lokasi Konsultasi</span>
                                <div class="mt-1">
                                    @if ($bookingKonsultasi->lokasi_konsultasi)
                                        <p class="text-sm font-semibold text-navy-dark">{{ $bookingKonsultasi->lokasi_konsultasi }}</p>
                                    @else
                                        <span class="text-sm font-semibold italic text-gray-400">Belum tersedia</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Catatan Konsultasi</span>
                            <p class="text-sm text-navy-dark mt-1 whitespace-pre-line leading-relaxed">
                                {{ $bookingKonsultasi->catatan_konsultasi ?: '–' }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Admin Konfirmasi</span>
                            <p class="text-sm font-semibold text-navy-dark mt-1">{{ $bookingKonsultasi->adminKonfirmasi?->nama ?? '–' }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4 divide-y divide-[#E2E8F0]">
                        <div class="pt-0">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Metode Konsultasi</span>
                            <x-status-badge :status="$metode" />
                        </div>
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Tanggal Booking</span>
                            <p class="text-sm font-bold text-navy-dark mt-1">
                                {{ $bookingKonsultasi->tanggal_booking ? \Carbon\Carbon::parse($bookingKonsultasi->tanggal_booking)->format('d M Y, H.i') : ($bookingKonsultasi->created_at ? $bookingKonsultasi->created_at->format('d M Y, H.i') : '–') }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Dikonfirmasi Pada</span>
                            <p class="text-sm font-semibold text-navy-dark mt-1">
                                {{ $bookingKonsultasi->dikonfirmasi_pada ? $bookingKonsultasi->dikonfirmasi_pada->format('d M Y, H.i') : '–' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert Box: Syarat Tandai Selesai -->
                <div class="bg-amber-50 border border-amber-400 border-l-4 rounded-xl p-4 text-amber-900 flex items-start gap-3">
                    <svg class="h-4 w-4 shrink-0 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="space-y-0.5">
                        <h4 class="text-sm font-semibold text-amber-900">Syarat Tandai Selesai</h4>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Pastikan konsultasi sudah terlaksana sebelum menandai booking sebagai selesai.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 4. Bottom Back Button -->
            <div class="flex justify-start pt-2">
                <a href="{{ route('admin.booking-konsultasi.index') }}" class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 rounded-xl px-5 py-2.5 text-sm font-semibold inline-flex items-center gap-2 transition shadow-sm">
                    <span>← Kembali ke Daftar Booking</span>
                </a>
            </div>
        </div>


        <!-- ============================================== -->
        <!-- VIEW 2: FORM KONFIRMASI (FIGMA NODE 84-3266)   -->
        <!-- ============================================== -->
        <div x-show="showForm" x-cloak class="space-y-5">
            <!-- Top Info Banner -->
            <div class="bg-blue-50 border border-blue-200 border-l-4 border-l-blue-600 rounded-xl p-4 text-blue-900 flex items-start gap-3">
                <svg class="h-4 w-4 shrink-0 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="space-y-0.5">
                    <h4 class="text-sm font-semibold text-blue-900">Konfirmasi Detail Teknis</h4>
                    <p class="text-xs text-blue-800 leading-relaxed">
                        Isi detail konsultasi sesuai metode yang dipilih Klien. Sistem tidak membuat integrasi rapat otomatis.
                    </p>
                </div>
            </div>

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

            <!-- Focused Form Card -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-navy-dark">
                        Metode {{ $metode === 'online' ? 'Online' : 'Offline' }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $metode === 'online' ? 'Link konsultasi diisi manual oleh Admin.' : 'Lokasi konsultasi diisi manual oleh Admin.' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.booking-konsultasi.konfirmasi', $bookingKonsultasi) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    @if ($metode === 'online')
                        <div>
                            <x-input-label for="link_konsultasi" :value="__('Link Konsultasi')" />
                            <x-text-input id="link_konsultasi" name="link_konsultasi" type="url" :value="old('link_konsultasi', $bookingKonsultasi->link_konsultasi)" required placeholder="Masukkan link konsultasi" class="w-full text-navy-dark" />
                            @if($errors->has('link_konsultasi'))
                                <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('link_konsultasi') }}</div>
                            @endif
                        </div>
                    @else
                        <div>
                            <x-input-label for="lokasi_konsultasi" :value="__('Lokasi Konsultasi')" />
                            <x-text-input id="lokasi_konsultasi" name="lokasi_konsultasi" type="text" :value="old('lokasi_konsultasi', $bookingKonsultasi->lokasi_konsultasi)" required placeholder="Masukkan lokasi konsultasi" class="w-full text-navy-dark" />
                            @if($errors->has('lokasi_konsultasi'))
                                <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('lokasi_konsultasi') }}</div>
                            @endif
                        </div>
                    @endif

                    <div>
                        <x-input-label for="catatan_konsultasi" :value="__('Catatan Admin')" />
                        <x-text-input tag="textarea" id="catatan_konsultasi" name="catatan_konsultasi" rows="4" placeholder="{{ $metode === 'online' ? 'Tuliskan catatan teknis untuk Klien...' : 'Tuliskan catatan lokasi atau instruksi hadir...' }}" class="w-full text-navy-dark">{{ old('catatan_konsultasi', $bookingKonsultasi->catatan_konsultasi) }}</x-text-input>
                        @if($errors->has('catatan_konsultasi'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('catatan_konsultasi') }}</div>
                        @endif
                    </div>

                    @if ($metode === 'online')
                        <!-- Catatan Online Alert Box -->
                        <div class="bg-amber-50 border border-amber-400 border-l-4 rounded-xl p-4 text-amber-900 flex items-start gap-3">
                            <svg class="h-4 w-4 shrink-0 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-semibold text-amber-900">Catatan Online</h4>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Link konsultasi diisi manual oleh Admin. Tidak ada integrasi otomatis.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end pt-4 border-t border-[#E2E8F0] gap-3">
                        <x-secondary-button type="button" @click="showForm = false">
                            {{ __('Batal') }}
                        </x-secondary-button>
                        <x-primary-button type="submit" class="gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ __('Simpan Konfirmasi') }}</span>
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
