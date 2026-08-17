<x-app-layout title="Detail Permintaan Reschedule" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Permintaan Reschedule', 'url' => route('admin.permintaan-reschedule.index')], ['label' => 'RS-' . str_pad($permintaanReschedule->id_reschedule, 3, '0', STR_PAD_LEFT)]]">

    @php
        $bookingLama = $permintaanReschedule->bookingLama;
        $bookingBaru = $permintaanReschedule->bookingBaru;
        $pengajuan = $bookingLama?->praPendaftaranPerkara;
        $jadwalLama = $bookingLama?->jadwalKonsultasi;
        $jadwalBaru = $permintaanReschedule->jadwalBaru ?? $bookingBaru?->jadwalKonsultasi;
        $canProcess = $permintaanReschedule->status_reschedule === 'menunggu_persetujuan';
    @endphp

    <div class="space-y-5">
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

        <!-- 1. Hero Card: Title, Status Badges, & Actions -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="space-y-2">
                <h2 class="text-xl font-bold text-navy-dark leading-snug">
                    RS-{{ str_pad($permintaanReschedule->id_reschedule, 3, '0', STR_PAD_LEFT) }} — {{ $pengajuan?->judul_perkara ?? 'Permintaan Reschedule' }}
                </h2>
                <p class="text-sm text-gray-500 leading-normal">
                    @if ($permintaanReschedule->status_reschedule === 'disetujui')
                        Permintaan reschedule telah disetujui. Booking baru telah dibuat dan aktif.
                    @elseif ($permintaanReschedule->status_reschedule === 'ditolak')
                        Permintaan reschedule telah ditolak. Jadwal konsultasi lama tetap berlaku.
                    @else
                        Permintaan reschedule menunggu tinjauan dan keputusan Admin.
                    @endif
                </p>
                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <x-status-badge :status="$permintaanReschedule->status_reschedule" />
                    <x-status-badge :status="$bookingLama?->status_booking ?? 'aktif'" />
                </div>
            </div>

            @if ($canProcess)
                <div class="flex flex-wrap items-center gap-2.5 shrink-0 self-start md:self-center">
                    <a href="#action-section" class="bg-navy-primary hover:bg-navy-dark text-white rounded-xl px-5 py-2.5 text-sm font-semibold transition shadow-sm inline-flex items-center gap-2">
                        <span>{{ __('Proses Permintaan') }}</span>
                    </a>
                </div>
            @endif
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
                            <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $permintaanReschedule->klien?->nama ?? '-' }}</p>
                        </div>
                        <div class="pt-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Email</span>
                            <p class="text-sm font-semibold text-navy-dark mt-0.5 break-all">{{ $permintaanReschedule->klien?->email ?? '-' }}</p>
                        </div>
                        <div class="pt-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Nomor Telepon</span>
                            <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $permintaanReschedule->klien?->no_telepon ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Informasi Perkara & Pengajuan -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="border-b border-[#F1F5F9] pb-3 mb-4">
                        <h3 class="text-sm font-bold text-navy-dark">Informasi Perkara</h3>
                    </div>
                    <div class="space-y-3 divide-y divide-[#E2E8F0]">
                        <div class="pt-0">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Kode Pengajuan</span>
                            <p class="text-sm font-semibold font-mono text-accent-blue mt-0.5">
                                {{ $pengajuan ? 'PP-' . str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) : '-' }}
                            </p>
                        </div>
                        <div class="pt-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Judul Perkara</span>
                            <p class="text-sm font-semibold text-navy-dark mt-0.5 leading-snug">{{ $pengajuan?->judul_perkara ?? '-' }}</p>
                        </div>
                        <div class="pt-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Kategori Perkara</span>
                            <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Jadwal Konsultasi Lama -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="border-b border-[#F1F5F9] pb-3 mb-4">
                        <h3 class="text-sm font-bold text-navy-dark">Jadwal Konsultasi Lama</h3>
                    </div>
                    <div class="space-y-3 divide-y divide-[#E2E8F0]">
                        <div class="pt-0">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Kode Booking Lama</span>
                            <p class="text-sm font-semibold font-mono text-accent-blue mt-0.5">
                                {{ $bookingLama ? 'BK-' . str_pad($bookingLama->id_booking, 3, '0', STR_PAD_LEFT) : '-' }}
                            </p>
                        </div>
                        <div class="pt-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Jadwal Lama</span>
                            <p class="text-sm font-semibold text-navy-dark mt-0.5">{{ $jadwalLama?->tanggal?->format('d M Y') ?? '-' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $jadwalLama ? substr((string) $jadwalLama->waktu_mulai, 0, 5) : '-' }}
                                @if ($jadwalLama)
                                    – {{ substr((string) $jadwalLama->waktu_selesai, 0, 5) }} WIB
                                @endif
                            </p>
                        </div>
                        <div class="pt-3">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Metode Lama</span>
                            <x-status-badge :status="$bookingLama?->metode_konsultasi ?? 'offline'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Detail Pengajuan Reschedule Card -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm space-y-6">
            <div class="border-b border-[#F1F5F9] pb-4">
                <h3 class="text-lg font-bold text-navy-dark">Informasi Pengajuan Reschedule</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-5">
                <!-- Left Column -->
                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Status Reschedule</span>
                        <x-status-badge :status="$permintaanReschedule->status_reschedule" />
                    </div>
                    <div class="pt-3.5">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Tanggal Pengajuan</span>
                        <p class="text-sm font-semibold text-navy-dark mt-1">
                            {{ $permintaanReschedule->tanggal_pengajuan ? $permintaanReschedule->tanggal_pengajuan->format('d M Y, H.i') : '–' }}
                        </p>
                    </div>
                    <div class="pt-3.5">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Alasan Pengajuan Reschedule</span>
                        <div class="mt-2 text-sm text-navy-dark leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl whitespace-pre-line font-medium">
                            {{ $permintaanReschedule->alasan_reschedule }}
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1">Preferensi Metode Baru</span>
                        <x-status-badge :status="$permintaanReschedule->preferensi_metode ?: 'offline'" />
                    </div>
                    <div class="pt-3.5">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Preferensi Jadwal Klien</span>
                        <div class="mt-2 text-sm text-navy-dark leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl font-medium">
                            {{ $permintaanReschedule->preferensi_jadwal ?: 'Klien tidak menyertakan preferensi khusus.' }}
                        </div>
                    </div>

                    @if ($permintaanReschedule->status_reschedule !== 'menunggu_persetujuan')
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Tanggal Keputusan</span>
                            <p class="text-sm font-semibold text-navy-dark mt-1">
                                {{ $permintaanReschedule->tanggal_keputusan ? $permintaanReschedule->tanggal_keputusan->format('d M Y, H.i') : '–' }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Catatan Admin</span>
                            <div class="mt-2 text-sm text-navy-dark leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl whitespace-pre-line">
                                {{ $permintaanReschedule->catatan_admin ?: 'Tidak ada catatan tambahan.' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($jadwalBaru || $bookingBaru)
            <!-- 4. Hasil Reschedule Card (If Disetujui) -->
            <div class="bg-white border border-green-200 rounded-xl p-6 shadow-sm space-y-4">
                <div class="border-b border-[#F1F5F9] pb-3">
                    <h3 class="text-lg font-bold text-green-700">Hasil Reschedule (Jadwal Baru Terkonfirmasi)</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Detail slot waktu dan booking baru yang telah disetujui.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-[#E2E8F0]">
                    <div class="space-y-2 py-1">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Slot Jadwal Baru</span>
                        <p class="text-sm font-bold text-navy-dark mt-1">
                            {{ $jadwalBaru?->tanggal?->format('d M Y') ?? '-' }}
                            · <span class="font-mono text-sm font-semibold">{{ $jadwalBaru ? substr((string) $jadwalBaru->waktu_mulai, 0, 5) : '-' }} – {{ $jadwalBaru ? substr((string) $jadwalBaru->waktu_selesai, 0, 5) : '-' }} WIB</span>
                        </p>
                    </div>
                    <div class="space-y-2 md:pl-6 py-1">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Status Booking Baru</span>
                        <div class="mt-1 flex items-center gap-3">
                            <span class="text-sm font-mono font-semibold text-accent-blue">
                                {{ $bookingBaru ? 'BK-' . str_pad($bookingBaru->id_booking, 3, '0', STR_PAD_LEFT) : '-' }}
                            </span>
                            <x-status-badge :status="$bookingBaru?->status_booking ?? 'aktif'" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- 5. Action Cards (If Waiting Approval) -->
        @if ($canProcess)
            <div id="action-section" class="grid grid-cols-1 lg:grid-cols-2 gap-5 pt-2">
                <!-- Card Action 1: Setujui Reschedule -->
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-bold text-navy-dark">Setujui Permintaan Reschedule</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Pilih slot jadwal pengganti yang tersedia untuk menyetujui penundaan.</p>
                        </div>

                        <form id="approve-form" method="POST" action="{{ route('admin.permintaan-reschedule.setujui', $permintaanReschedule) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="id_jadwal_baru" :value="__('Pilih Slot Jadwal Baru')" />
                                <x-select id="id_jadwal_baru" name="id_jadwal_baru" required class="text-navy-dark">
                                    <option value="">{{ __('Pilih jadwal tersedia') }}</option>
                                    @foreach ($jadwalTersedia as $jadwal)
                                        <option value="{{ $jadwal->id_jadwal }}" @selected(old('id_jadwal_baru') == $jadwal->id_jadwal)>
                                            {{ $jadwal->tanggal?->format('d M Y') }} · {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }} WIB
                                        </option>
                                    @endforeach
                                </x-select>
                                @if($errors->has('id_jadwal_baru'))
                                    <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('id_jadwal_baru') }}</div>
                                @endif
                            </div>

                            <div>
                                <x-input-label for="catatan_admin_approve" :value="__('Catatan Persetujuan (Opsional)')" />
                                <x-text-input tag="textarea" id="catatan_admin_approve" name="catatan_admin" rows="4" placeholder="Tulis catatan, misalnya: 'Jadwal baru disetujui sesuai preferensi.'" class="w-full text-navy-dark">{{ old('catatan_admin') }}</x-text-input>
                            </div>
                        </form>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-[#E2E8F0]">
                        <x-primary-button type="submit" form="approve-form" class="gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ __('Setujui Reschedule') }}</span>
                        </x-primary-button>
                    </div>
                </div>

                <!-- Card Action 2: Tolak Reschedule -->
                <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-bold text-rose-600">Tolak Permintaan Reschedule</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Tolak permintaan jika jadwal pengganti tidak dapat diakomodasi.</p>
                        </div>

                        <!-- Alert Box: Efek Penolakan -->
                        <div class="bg-amber-50 border border-amber-400 border-l-4 rounded-xl p-4 text-amber-900 flex items-start gap-3">
                            <svg class="h-4 w-4 shrink-0 text-amber-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="space-y-0.5">
                                <h4 class="text-sm font-semibold text-amber-900">Efek Penolakan</h4>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    Jika ditolak, jadwal konsultasi lama tetap berlaku dan booking lama tetap aktif.
                                </p>
                            </div>
                        </div>

                        <form id="reject-form" method="POST" action="{{ route('admin.permintaan-reschedule.tolak', $permintaanReschedule) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="catatan_admin_reject" :value="__('Catatan Penolakan Admin')" />
                                <x-text-input tag="textarea" id="catatan_admin_reject" name="catatan_admin" rows="4" required placeholder="Jelaskan alasan penolakan agar klien memahami keputusan ini..." class="w-full text-navy-dark">{{ old('catatan_admin') }}</x-text-input>
                                @if($errors->has('catatan_admin'))
                                    <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('catatan_admin') }}</div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-[#E2E8F0]">
                        <x-danger-button type="submit" form="reject-form" onclick="return confirm('Apakah Anda yakin ingin menolak permintaan reschedule ini?')" class="gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>{{ __('Tolak Reschedule') }}</span>
                        </x-danger-button>
                    </div>
                </div>
            </div>
        @endif

        <!-- 6. Bottom Back Button -->
        <div class="flex justify-start pt-2">
            <a href="{{ route('admin.permintaan-reschedule.index') }}" class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 rounded-xl px-5 py-2.5 text-sm font-semibold inline-flex items-center gap-2 transition shadow-sm">
                <span>← Kembali ke Daftar Reschedule</span>
            </a>
        </div>
    </div>
</x-app-layout>
