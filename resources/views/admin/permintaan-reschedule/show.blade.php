<x-app-layout title="Detail Permintaan Reschedule" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Permintaan Reschedule', 'url' => route('admin.permintaan-reschedule.index')], ['label' => 'RS-' . str_pad($permintaanReschedule->id_reschedule, 3, '0', STR_PAD_LEFT)]]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <a href="{{ route('admin.permintaan-reschedule.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </a>
        </div>

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
        $canProcess = $permintaanReschedule->status_reschedule === 'menunggu_persetujuan';
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

        <!-- Grid Layout for Reschedule Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Left Card: Klien & Pengajuan Info -->
            <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Informasi Klien & Perkara</h3>
                    <p class="text-xs text-gray-400 mt-1">Detail pemohon reschedule dan perkara terkait.</p>
                </div>

                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0 flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $permintaanReschedule->klien?->nama ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $permintaanReschedule->klien?->email ?? '-' }}</span>
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

            <!-- Right Card: Jadwal & Booking Lama -->
            <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Jadwal & Booking Lama</h3>
                    <p class="text-xs text-gray-400 mt-1">Detail slot waktu pelaksanaan konsultasi lama.</p>
                </div>

                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0 flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Konsultasi Lama</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $jadwalLama?->tanggal?->format('d M Y') ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Waktu Konsultasi Lama</span>
                        <span class="text-sm font-semibold text-navy-dark font-mono">
                            {{ $jadwalLama ? substr((string) $jadwalLama->waktu_mulai, 0, 5) : '-' }}
                            @if ($jadwalLama)
                                - {{ substr((string) $jadwalLama->waktu_selesai, 0, 5) }}
                            @endif
                        </span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Booking Lama</span>
                        <x-status-badge :status="$bookingLama?->status_booking ?? 'aktif'" />
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Metode Lama</span>
                        <x-status-badge :status="$bookingLama?->metode_konsultasi ?? 'offline'" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Permintaan Reschedule Card -->
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
            <div>
                <h3 class="font-bold text-navy-dark text-lg">Informasi Pengajuan Reschedule</h3>
                <p class="text-xs text-gray-400 mt-1">Alasan penundaan dan preferensi jadwal baru klien.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-[#E2E8F0]">
                <!-- Alasan & Status -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Reschedule</span>
                            <div class="mt-1">
                                <x-status-badge :status="$permintaanReschedule->status_reschedule" />
                            </div>
                        </div>
                        <div>
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                            <div class="text-xs font-semibold text-navy-dark mt-1.5">{{ $permintaanReschedule->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[#E2E8F0]">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Alasan Pengajuan Reschedule</span>
                        <div class="mt-2 text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl whitespace-pre-line font-medium">
                            {{ $permintaanReschedule->alasan_reschedule }}
                        </div>
                    </div>
                </div>

                <!-- Preferensi Baru & Keputusan -->
                <div class="space-y-4 md:pl-6 pt-6 md:pt-0">
                    <div>
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Preferensi Jadwal Baru Klien</span>
                        <div class="mt-2 text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl font-medium">
                            {{ $permintaanReschedule->preferensi_jadwal ?: '-' }}
                        </div>
                    </div>

                    <div>
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Preferensi Metode Baru</span>
                        <div class="mt-2">
                            <x-status-badge :status="$permintaanReschedule->preferensi_metode ?: 'offline'" />
                        </div>
                    </div>

                    @if ($permintaanReschedule->status_reschedule !== 'menunggu_persetujuan')
                        <div class="pt-4 border-t border-[#E2E8F0] space-y-3">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Keputusan</span>
                                    <div class="text-xs font-semibold text-navy-dark mt-1">{{ $permintaanReschedule->tanggal_keputusan?->format('d M Y H:i') ?? '-' }}</div>
                                </div>
                            </div>
                            <div>
                                <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Catatan Admin</span>
                                <div class="text-xs text-gray-600 bg-gray-50 border border-gray-200 p-3 rounded-xl mt-1.5 whitespace-pre-line">
                                    {{ $permintaanReschedule->catatan_admin ?: 'Tidak ada catatan.' }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        @if ($jadwalBaru || $bookingBaru)
            <!-- Jadwal Baru Card (If Approved) -->
            <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6">
                <div>
                    <h3 class="font-bold text-[#059669] text-lg">Hasil Reschedule (Jadwal Baru)</h3>
                    <p class="text-xs text-gray-400 mt-1">Detail slot waktu baru yang disetujui.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 divide-y md:divide-y-0 md:divide-x divide-[#E2E8F0]">
                    <div class="space-y-2 py-2">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Slot Jadwal Baru</span>
                        <div class="text-sm font-bold text-navy-dark mt-1">
                            {{ $jadwalBaru?->tanggal?->format('d M Y') ?? '-' }} 
                            · <span class="font-mono">{{ $jadwalBaru ? substr((string) $jadwalBaru->waktu_mulai, 0, 5) : '-' }} - {{ $jadwalBaru ? substr((string) $jadwalBaru->waktu_selesai, 0, 5) : '-' }}</span>
                        </div>
                    </div>
                    <div class="space-y-2 md:pl-6 py-2">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block">Status Booking Baru</span>
                        <div class="mt-1.5">
                            <x-status-badge :status="$bookingBaru?->status_booking ?? 'aktif'" />
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Cards (If Waiting Approval) -->
        @if ($canProcess)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Card Action 1: Setujui Reschedule -->
                <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-bold text-[#1e3a8a] text-lg">Setujui Permintaan Reschedule</h3>
                            <p class="text-xs text-gray-400 mt-1">Pilih slot jadwal pengganti yang kosong untuk disepakati.</p>
                        </div>

                        <form id="approve-form" method="POST" action="{{ route('admin.permintaan-reschedule.setujui', $permintaanReschedule) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="id_jadwal_baru" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Pilih Slot Jadwal Baru</label>
                                <select id="id_jadwal_baru" name="id_jadwal_baru" required class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                                    <option value="">{{ __('Pilih jadwal tersedia') }}</option>
                                    @foreach ($jadwalTersedia as $jadwal)
                                        <option value="{{ $jadwal->id_jadwal }}" @selected(old('id_jadwal_baru') == $jadwal->id_jadwal)>
                                            {{ $jadwal->tanggal?->format('d M Y') }} · {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('id_jadwal_baru'))
                                    <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('id_jadwal_baru') }}</div>
                                @endif
                            </div>

                            <div>
                                <label for="catatan_admin_approve" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Catatan Persetujuan (Opsional)</label>
                                <textarea id="catatan_admin_approve" name="catatan_admin" rows="4" placeholder="Tulis catatan, misalnya: 'Jadwal baru disetujui sesuai preferensi.'"
                                    class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm p-4">{{ old('catatan_admin') }}</textarea>
                            </div>
                        </form>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-[#E2E8F0] mt-6">
                        <button type="submit" form="approve-form" class="inline-flex items-center justify-center bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest">
                            {{ __('Setujui Reschedule') }}
                        </button>
                    </div>
                </div>

                <!-- Card Action 2: Tolak Reschedule -->
                <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-bold text-[#b91c1c] text-lg">Tolak Permintaan Reschedule</h3>
                            <p class="text-xs text-gray-400 mt-1">Tolak reschedule jika jadwal slot yang diajukan tidak relevan.</p>
                        </div>

                        <!-- Alert Box: Efek Penolakan -->
                        <div class="bg-[#FFFBEB] border border-[#F59E0B] text-[#92400E] p-4 rounded-xl text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-[#F59E0B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <span>Efek Penolakan</span>
                            </div>
                            <p class="opacity-90 pl-5.5">Jika ditolak, jadwal konsultasi lama tetap berlaku dan booking lama tetap aktif.</p>
                        </div>

                        <form id="reject-form" method="POST" action="{{ route('admin.permintaan-reschedule.tolak', $permintaanReschedule) }}" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <!-- Read-only inputs representing the Figma fields -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xxs font-bold text-gray-400 uppercase tracking-wider mb-2">Kode Reschedule</label>
                                    <input type="text" disabled value="RS-{{ str_pad($permintaanReschedule->id_reschedule, 3, '0', STR_PAD_LEFT) }}" class="w-full bg-gray-50 border-[#E2E8F0] text-gray-500 rounded-xl text-xs px-4 h-10 font-mono">
                                </div>
                                <div>
                                    <label class="block text-xxs font-bold text-gray-400 uppercase tracking-wider mb-2">Booking Lama</label>
                                    <input type="text" disabled value="BK-{{ str_pad($bookingLama?->id_booking, 3, '0', STR_PAD_LEFT) }} — tetap aktif" class="w-full bg-gray-50 border-[#E2E8F0] text-gray-500 rounded-xl text-xs px-4 h-10 font-mono">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xxs font-bold text-gray-400 uppercase tracking-wider mb-2">Alasan Klien</label>
                                <textarea disabled class="w-full bg-gray-50 border-[#E2E8F0] text-gray-500 rounded-xl text-xs p-3 h-20 resize-none">{{ $permintaanReschedule->alasan_reschedule }}</textarea>
                            </div>

                            <div>
                                <label for="catatan_admin_reject" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Catatan Penolakan Admin (Wajib)</label>
                                <textarea id="catatan_admin_reject" name="catatan_admin" rows="3" required placeholder="Jelaskan alasan penolakan agar klien paham..."
                                    class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm p-4">{{ old('catatan_admin') }}</textarea>
                                @if($errors->has('catatan_admin'))
                                    <div class="text-rose-600 text-xs font-semibold mt-1.5">{{ $errors->first('catatan_admin') }}</div>
                                @endif
                            </div>
                        </form>
                    </div>

                    <div class="flex justify-end pt-4 border-t border-[#E2E8F0] mt-6">
                        <button type="submit" form="reject-form" class="inline-flex items-center justify-center bg-[#b91c1c] hover:bg-red-850 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-red-800/20 uppercase tracking-widest">
                            {{ __('Tolak Reschedule') }}
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
