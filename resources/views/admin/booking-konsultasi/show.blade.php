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
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div class="space-y-2">
                    <h2 class="text-[18px] font-bold text-[#0f172a] leading-snug">
                        BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }} — {{ $pengajuan?->judul_perkara ?? 'Konsultasi Perkara' }}
                    </h2>
                    <p class="text-[13px] text-[#64748b] leading-normal">
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
                        <button @click="showForm = true" type="button" class="bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-[14px] px-5 py-2.5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                            <span>{{ $statusKonfirmasi === 'terkonfirmasi' ? __('Perbarui Konfirmasi') : __('Konfirmasi Konsultasi') }}</span>
                        </button>
                    @endif

                    @if ($canComplete)
                        <form method="POST" action="{{ route('admin.booking-konsultasi.selesai', $bookingKonsultasi) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" onclick="return confirm('Apakah Anda yakin sesi konsultasi telah selesai? Status tidak dapat dikembalikan.')" class="bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#334155] rounded-[14px] px-5 py-2.5 text-[13px] font-semibold transition shadow-sm">
                                {{ __('Tandai Selesai') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- 2. Middle 3-Column Info Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <!-- Card 1: Informasi Klien -->
                <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="border-b border-[#f1f5f9] pb-3 mb-4">
                            <h3 class="text-[14px] font-bold text-[#0f172a]">Informasi Klien</h3>
                        </div>
                        <div class="space-y-3 divide-y divide-[#e2e8f0]/60">
                            <div class="pt-0">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Nama</span>
                                <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $bookingKonsultasi->klien?->nama ?? '-' }}</p>
                            </div>
                            <div class="pt-3">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Email</span>
                                <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5 break-all">{{ $bookingKonsultasi->klien?->email ?? '-' }}</p>
                            </div>
                            <div class="pt-3">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Nomor Telepon</span>
                                <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $bookingKonsultasi->klien?->no_telepon ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Informasi Pengajuan -->
                <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="border-b border-[#f1f5f9] pb-3 mb-4">
                            <h3 class="text-[14px] font-bold text-[#0f172a]">Informasi Pengajuan</h3>
                        </div>
                        <div class="space-y-3 divide-y divide-[#e2e8f0]/60">
                            <div class="pt-0">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Kode Pengajuan</span>
                                <p class="text-[13px] font-semibold font-mono text-[#1e3a8a] mt-0.5">
                                    {{ $pengajuan ? 'PP-' . str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) : '-' }}
                                </p>
                            </div>
                            <div class="pt-3">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Kategori Perkara</span>
                                <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</p>
                            </div>
                            <div class="pt-3">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block mb-1">Status Pengajuan</span>
                                @if ($pengajuan?->status_pengajuan)
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                @else
                                    <span class="text-[13px] text-gray-400">-</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Jadwal dan Metode -->
                <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="border-b border-[#f1f5f9] pb-3 mb-4">
                            <h3 class="text-[14px] font-bold text-[#0f172a]">Jadwal dan Metode</h3>
                        </div>
                        <div class="space-y-3 divide-y divide-[#e2e8f0]/60">
                            <div class="pt-0">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Jadwal</span>
                                <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</p>
                                <p class="text-[12px] text-[#64748b] mt-0.5">
                                    {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) : '-' }}
                                    @if ($jadwal)
                                        – {{ substr((string) $jadwal->waktu_selesai, 0, 5) }} WIB
                                    @endif
                                </p>
                            </div>
                            <div class="pt-3">
                                <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block mb-1">Metode</span>
                                <x-status-badge :status="$metode" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Bottom Card: Informasi Konsultasi -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm space-y-6">
                <div class="border-b border-[#f1f5f9] pb-4">
                    <h3 class="text-[16px] font-bold text-[#0f172a]">Informasi Konsultasi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-5">
                    <!-- Left Column -->
                    <div class="space-y-4 divide-y divide-[#e2e8f0]/60">
                        <div class="pt-0">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block mb-1">Status Booking</span>
                            <x-status-badge :status="$bookingKonsultasi->status_booking" />
                        </div>
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block mb-1">Status Konfirmasi</span>
                            <x-status-badge :status="$statusKonfirmasi" />
                        </div>
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Catatan Preferensi Klien</span>
                            <p class="text-[13px] text-[#0f172a] mt-1 whitespace-pre-line leading-relaxed">
                                {{ $bookingKonsultasi->catatan_preferensi_klien ?: '–' }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            @if ($metode === 'online')
                                <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Link Konsultasi</span>
                                <div class="mt-1">
                                    @if ($bookingKonsultasi->link_konsultasi)
                                        <a href="{{ $bookingKonsultasi->link_konsultasi }}" class="text-[13px] font-semibold text-accent-blue hover:underline break-all inline-flex items-center gap-1.5" target="_blank" rel="noopener noreferrer">
                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            <span>{{ $bookingKonsultasi->link_konsultasi }}</span>
                                        </a>
                                    @else
                                        <span class="text-[13px] font-semibold italic text-[#94a3b8]">Belum tersedia</span>
                                    @endif
                                </div>
                            @else
                                <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Lokasi Konsultasi</span>
                                <div class="mt-1">
                                    @if ($bookingKonsultasi->lokasi_konsultasi)
                                        <p class="text-[13px] font-semibold text-[#0f172a]">{{ $bookingKonsultasi->lokasi_konsultasi }}</p>
                                    @else
                                        <span class="text-[13px] font-semibold italic text-[#94a3b8]">Belum tersedia</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Catatan Konsultasi</span>
                            <p class="text-[13px] text-[#0f172a] mt-1 whitespace-pre-line leading-relaxed">
                                {{ $bookingKonsultasi->catatan_konsultasi ?: '–' }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Admin Konfirmasi</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-1">{{ $bookingKonsultasi->adminKonfirmasi?->nama ?? '–' }}</p>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-4 divide-y divide-[#e2e8f0]/60">
                        <div class="pt-0">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block mb-1">Metode Konsultasi</span>
                            <x-status-badge :status="$metode" />
                        </div>
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Tanggal Booking</span>
                            <p class="text-[14px] font-bold text-[#0f172a] mt-1">
                                {{ $bookingKonsultasi->tanggal_booking ? \Carbon\Carbon::parse($bookingKonsultasi->tanggal_booking)->format('d M Y, H.i') : ($bookingKonsultasi->created_at ? $bookingKonsultasi->created_at->format('d M Y, H.i') : '–') }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Dikonfirmasi Pada</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-1">
                                {{ $bookingKonsultasi->dikonfirmasi_pada ? $bookingKonsultasi->dikonfirmasi_pada->format('d M Y, H.i') : '–' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Alert Box: Syarat Tandai Selesai -->
                <div class="bg-[#fffbeb] border border-[#f59e0b] border-l-4 rounded-[14px] p-4 text-[#92400e] flex items-start gap-3">
                    <svg class="h-4 w-4 shrink-0 text-[#f59e0b] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="space-y-0.5">
                        <h4 class="text-[13px] font-semibold text-[#92400e]">Syarat Tandai Selesai</h4>
                        <p class="text-[12px] text-[#92400e]/90 leading-relaxed">
                            Pastikan konsultasi sudah terlaksana sebelum menandai booking sebagai selesai.
                        </p>
                    </div>
                </div>
            </div>

            <!-- 4. Bottom Back Button -->
            <div class="flex justify-start pt-2">
                <a href="{{ route('admin.booking-konsultasi.index') }}" class="bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#334155] rounded-[14px] px-5 py-2.5 text-[13px] font-semibold inline-flex items-center gap-2 transition shadow-sm">
                    <span>← Kembali ke Daftar Booking</span>
                </a>
            </div>
        </div>


        <!-- ============================================== -->
        <!-- VIEW 2: FORM KONFIRMASI (FIGMA NODE 84-3266)   -->
        <!-- ============================================== -->
        <div x-show="showForm" x-cloak class="space-y-5">
            <!-- Top Info Banner -->
            <div class="bg-[#eff6ff] border border-[#bfdbfe] border-l-4 border-l-[#1d4ed8] rounded-[14px] p-4 text-[#1e40af] flex items-start gap-3">
                <svg class="h-4 w-4 shrink-0 text-[#1d4ed8] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="space-y-0.5">
                    <h4 class="text-[13px] font-semibold text-[#1e40af]">Konfirmasi Detail Teknis</h4>
                    <p class="text-[12px] text-[#1e40af]/90 leading-relaxed">
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
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <h3 class="text-[16px] font-bold text-[#0f172a]">
                        Metode {{ $metode === 'online' ? 'Online' : 'Offline' }}
                    </h3>
                    <p class="text-[13px] text-[#64748b] mt-1">
                        {{ $metode === 'online' ? 'Link konsultasi diisi manual oleh Admin.' : 'Lokasi konsultasi diisi manual oleh Admin.' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('admin.booking-konsultasi.konfirmasi', $bookingKonsultasi) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    @if ($metode === 'online')
                        <div>
                            <label for="link_konsultasi" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Link Konsultasi <span class="text-rose-500">*</span></label>
                            <input id="link_konsultasi" name="link_konsultasi" type="url" value="{{ old('link_konsultasi', $bookingKonsultasi->link_konsultasi) }}" required placeholder="Masukkan link konsultasi"
                                class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                            @if($errors->has('link_konsultasi'))
                                <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('link_konsultasi') }}</div>
                            @endif
                        </div>
                    @else
                        <div>
                            <label for="lokasi_konsultasi" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Lokasi Konsultasi <span class="text-rose-500">*</span></label>
                            <input id="lokasi_konsultasi" name="lokasi_konsultasi" type="text" value="{{ old('lokasi_konsultasi', $bookingKonsultasi->lokasi_konsultasi) }}" required placeholder="Masukkan lokasi konsultasi"
                                class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                            @if($errors->has('lokasi_konsultasi'))
                                <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('lokasi_konsultasi') }}</div>
                            @endif
                        </div>
                    @endif

                    <div>
                        <label for="catatan_konsultasi" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Catatan Admin</label>
                        <textarea id="catatan_konsultasi" name="catatan_konsultasi" rows="4" placeholder="{{ $metode === 'online' ? 'Tuliskan catatan teknis untuk Klien...' : 'Tuliskan catatan lokasi atau instruksi hadir...' }}"
                            class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm p-4 text-[#0f172a]">{{ old('catatan_konsultasi', $bookingKonsultasi->catatan_konsultasi) }}</textarea>
                        @if($errors->has('catatan_konsultasi'))
                            <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('catatan_konsultasi') }}</div>
                        @endif
                    </div>

                    @if ($metode === 'online')
                        <!-- Catatan Online Alert Box -->
                        <div class="bg-[#fffbeb] border border-[#f59e0b] border-l-4 rounded-[14px] p-4 text-[#92400e] flex items-start gap-3">
                            <svg class="h-4 w-4 shrink-0 text-[#f59e0b] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="space-y-0.5">
                                <h4 class="text-[13px] font-semibold text-[#92400e]">Catatan Online</h4>
                                <p class="text-[12px] text-[#92400e]/90 leading-relaxed">
                                    Link konsultasi diisi manual oleh Admin. Tidak ada integrasi otomatis.
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end pt-4 border-t border-[#e2e8f0] gap-3">
                        <button type="button" @click="showForm = false" class="bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#334155] rounded-[14px] px-5 py-2.5 text-[13px] font-semibold transition shadow-sm">
                            {{ __('Batal') }}
                        </button>
                        <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-[14px] px-5 py-2.5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ __('Simpan Konfirmasi') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
