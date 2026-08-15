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
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="space-y-2">
                <h2 class="text-[18px] font-bold text-[#0f172a] leading-snug">
                    RS-{{ str_pad($permintaanReschedule->id_reschedule, 3, '0', STR_PAD_LEFT) }} — {{ $pengajuan?->judul_perkara ?? 'Permintaan Reschedule' }}
                </h2>
                <p class="text-[13px] text-[#64748b] leading-normal">
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
                    <a href="#action-section" class="bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-[14px] px-5 py-2.5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                        <span>{{ __('Proses Permintaan') }}</span>
                    </a>
                </div>
            @endif
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
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $permintaanReschedule->klien?->nama ?? '-' }}</p>
                        </div>
                        <div class="pt-3">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Email</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5 break-all">{{ $permintaanReschedule->klien?->email ?? '-' }}</p>
                        </div>
                        <div class="pt-3">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Nomor Telepon</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $permintaanReschedule->klien?->no_telepon ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Informasi Perkara & Pengajuan -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="border-b border-[#f1f5f9] pb-3 mb-4">
                        <h3 class="text-[14px] font-bold text-[#0f172a]">Informasi Perkara</h3>
                    </div>
                    <div class="space-y-3 divide-y divide-[#e2e8f0]/60">
                        <div class="pt-0">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Kode Pengajuan</span>
                            <p class="text-[13px] font-semibold font-mono text-[#1e3a8a] mt-0.5">
                                {{ $pengajuan ? 'PP-' . str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) : '-' }}
                            </p>
                        </div>
                        <div class="pt-3">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Judul Perkara</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5 leading-snug">{{ $pengajuan?->judul_perkara ?? '-' }}</p>
                        </div>
                        <div class="pt-3">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Kategori Perkara</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Jadwal Konsultasi Lama -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 sm:p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="border-b border-[#f1f5f9] pb-3 mb-4">
                        <h3 class="text-[14px] font-bold text-[#0f172a]">Jadwal Konsultasi Lama</h3>
                    </div>
                    <div class="space-y-3 divide-y divide-[#e2e8f0]/60">
                        <div class="pt-0">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Kode Booking Lama</span>
                            <p class="text-[13px] font-semibold font-mono text-[#1e3a8a] mt-0.5">
                                {{ $bookingLama ? 'BK-' . str_pad($bookingLama->id_booking, 3, '0', STR_PAD_LEFT) : '-' }}
                            </p>
                        </div>
                        <div class="pt-3">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Jadwal Lama</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-0.5">{{ $jadwalLama?->tanggal?->format('d M Y') ?? '-' }}</p>
                            <p class="text-[12px] text-[#64748b] mt-0.5">
                                {{ $jadwalLama ? substr((string) $jadwalLama->waktu_mulai, 0, 5) : '-' }}
                                @if ($jadwalLama)
                                    – {{ substr((string) $jadwalLama->waktu_selesai, 0, 5) }} WIB
                                @endif
                            </p>
                        </div>
                        <div class="pt-3">
                            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block mb-1">Metode Lama</span>
                            <x-status-badge :status="$bookingLama?->metode_konsultasi ?? 'offline'" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Detail Pengajuan Reschedule Card -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm space-y-6">
            <div class="border-b border-[#f1f5f9] pb-4">
                <h3 class="text-[16px] font-bold text-[#0f172a]">Informasi Pengajuan Reschedule</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-5">
                <!-- Left Column -->
                <div class="space-y-4 divide-y divide-[#e2e8f0]/60">
                    <div class="pt-0">
                        <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block mb-1">Status Reschedule</span>
                        <x-status-badge :status="$permintaanReschedule->status_reschedule" />
                    </div>
                    <div class="pt-3.5">
                        <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Tanggal Pengajuan</span>
                        <p class="text-[13px] font-semibold text-[#0f172a] mt-1">
                            {{ $permintaanReschedule->tanggal_pengajuan ? $permintaanReschedule->tanggal_pengajuan->format('d M Y, H.i') : '–' }}
                        </p>
                    </div>
                    <div class="pt-3.5">
                        <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Alasan Pengajuan Reschedule</span>
                        <div class="mt-2 text-[13px] text-[#0f172a] leading-relaxed bg-[#f8fafc] border border-[#e2e8f0] p-4 rounded-[12px] whitespace-pre-line font-medium">
                            {{ $permintaanReschedule->alasan_reschedule }}
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-4 divide-y divide-[#e2e8f0]/60">
                    <div class="pt-0">
                        <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block mb-1">Preferensi Metode Baru</span>
                        <x-status-badge :status="$permintaanReschedule->preferensi_metode ?: 'offline'" />
                    </div>
                    <div class="pt-3.5">
                        <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Preferensi Jadwal Klien</span>
                        <div class="mt-2 text-[13px] text-[#0f172a] leading-relaxed bg-[#f8fafc] border border-[#e2e8f0] p-4 rounded-[12px] font-medium">
                            {{ $permintaanReschedule->preferensi_jadwal ?: 'Klien tidak menyertakan preferensi khusus.' }}
                        </div>
                    </div>

                    @if ($permintaanReschedule->status_reschedule !== 'menunggu_persetujuan')
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Tanggal Keputusan</span>
                            <p class="text-[13px] font-semibold text-[#0f172a] mt-1">
                                {{ $permintaanReschedule->tanggal_keputusan ? $permintaanReschedule->tanggal_keputusan->format('d M Y, H.i') : '–' }}
                            </p>
                        </div>
                        <div class="pt-3.5">
                            <span class="text-[11px] font-semibold text-[#64748b] tracking-[0.275px] uppercase block">Catatan Admin</span>
                            <div class="mt-2 text-[13px] text-[#0f172a] leading-relaxed bg-[#f8fafc] border border-[#e2e8f0] p-4 rounded-[12px] whitespace-pre-line">
                                {{ $permintaanReschedule->catatan_admin ?: 'Tidak ada catatan tambahan.' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($jadwalBaru || $bookingBaru)
            <!-- 4. Hasil Reschedule Card (If Disetujui) -->
            <div class="bg-white border border-[#bbf7d0] rounded-[16px] p-6 shadow-sm space-y-4">
                <div class="border-b border-[#f1f5f9] pb-3">
                    <h3 class="text-[16px] font-bold text-[#15803d]">Hasil Reschedule (Jadwal Baru Terkonfirmasi)</h3>
                    <p class="text-[12px] text-[#64748b] mt-0.5">Detail slot waktu dan booking baru yang telah disetujui.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-[#e2e8f0]">
                    <div class="space-y-2 py-1">
                        <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Slot Jadwal Baru</span>
                        <p class="text-[14px] font-bold text-[#0f172a] mt-1">
                            {{ $jadwalBaru?->tanggal?->format('d M Y') ?? '-' }}
                            · <span class="font-mono text-[13px] font-semibold">{{ $jadwalBaru ? substr((string) $jadwalBaru->waktu_mulai, 0, 5) : '-' }} – {{ $jadwalBaru ? substr((string) $jadwalBaru->waktu_selesai, 0, 5) : '-' }} WIB</span>
                        </p>
                    </div>
                    <div class="space-y-2 md:pl-6 py-1">
                        <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">Status Booking Baru</span>
                        <div class="mt-1 flex items-center gap-3">
                            <span class="text-[13px] font-mono font-semibold text-[#1e3a8a]">
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
                <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-[16px] font-bold text-[#1e3a8a]">Setujui Permintaan Reschedule</h3>
                            <p class="text-[12px] text-[#64748b] mt-0.5">Pilih slot jadwal pengganti yang tersedia untuk menyetujui penundaan.</p>
                        </div>

                        <form id="approve-form" method="POST" action="{{ route('admin.permintaan-reschedule.setujui', $permintaanReschedule) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="id_jadwal_baru" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Pilih Slot Jadwal Baru <span class="text-rose-500">*</span></label>
                                <select id="id_jadwal_baru" name="id_jadwal_baru" required class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                                    <option value="">{{ __('Pilih jadwal tersedia') }}</option>
                                    @foreach ($jadwalTersedia as $jadwal)
                                        <option value="{{ $jadwal->id_jadwal }}" @selected(old('id_jadwal_baru') == $jadwal->id_jadwal)>
                                            {{ $jadwal->tanggal?->format('d M Y') }} · {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }} WIB
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('id_jadwal_baru'))
                                    <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('id_jadwal_baru') }}</div>
                                @endif
                            </div>

                            <div>
                                <label for="catatan_admin_approve" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Catatan Persetujuan (Opsional)</label>
                                <textarea id="catatan_admin_approve" name="catatan_admin" rows="4" placeholder="Tulis catatan, misalnya: 'Jadwal baru disetujui sesuai preferensi.'"
                                    class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm p-4 text-[#0f172a]">{{ old('catatan_admin') }}</textarea>
                            </div>
                        </form>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-[#e2e8f0]">
                        <button type="submit" form="approve-form" class="bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-[14px] px-5 py-2.5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>{{ __('Setujui Reschedule') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Card Action 2: Tolak Reschedule -->
                <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-[16px] font-bold text-[#b91c1c]">Tolak Permintaan Reschedule</h3>
                            <p class="text-[12px] text-[#64748b] mt-0.5">Tolak permintaan jika jadwal pengganti tidak dapat diakomodasi.</p>
                        </div>

                        <!-- Alert Box: Efek Penolakan -->
                        <div class="bg-[#fffbeb] border border-[#f59e0b] border-l-4 rounded-[14px] p-4 text-[#92400e] flex items-start gap-3">
                            <svg class="h-4 w-4 shrink-0 text-[#f59e0b] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div class="space-y-0.5">
                                <h4 class="text-[13px] font-semibold text-[#92400e]">Efek Penolakan</h4>
                                <p class="text-[12px] text-[#92400e]/90 leading-relaxed">
                                    Jika ditolak, jadwal konsultasi lama tetap berlaku dan booking lama tetap aktif.
                                </p>
                            </div>
                        </div>

                        <form id="reject-form" method="POST" action="{{ route('admin.permintaan-reschedule.tolak', $permintaanReschedule) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="catatan_admin_reject" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Catatan Penolakan Admin <span class="text-rose-500">*</span></label>
                                <textarea id="catatan_admin_reject" name="catatan_admin" rows="4" required placeholder="Jelaskan alasan penolakan agar klien memahami keputusan ini..."
                                    class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#b91c1c] focus:ring focus:ring-[#b91c1c]/20 rounded-[12px] text-[13px] transition shadow-sm p-4 text-[#0f172a]">{{ old('catatan_admin') }}</textarea>
                                @if($errors->has('catatan_admin'))
                                    <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('catatan_admin') }}</div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-[#e2e8f0]">
                        <button type="submit" form="reject-form" onclick="return confirm('Apakah Anda yakin ingin menolak permintaan reschedule ini?')" class="bg-[#b91c1c] hover:bg-red-800 text-white rounded-[14px] px-5 py-2.5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span>{{ __('Tolak Reschedule') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- 6. Bottom Back Button -->
        <div class="flex justify-start pt-2">
            <a href="{{ route('admin.permintaan-reschedule.index') }}" class="bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#334155] rounded-[14px] px-5 py-2.5 text-[13px] font-semibold inline-flex items-center gap-2 transition shadow-sm">
                <span>← Kembali ke Daftar Reschedule</span>
            </a>
        </div>
    </div>
</x-app-layout>
