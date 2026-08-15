<x-app-layout title="Laporan & Rekapitulasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Laporan']]">

    <div class="space-y-5">
        <!-- 1. Hero Header Card -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="space-y-1.5">
                <h2 class="text-[18px] font-bold text-[#0f172a] leading-snug">
                    Laporan & Rekapitulasi Sistem
                </h2>
                <p class="text-[13px] text-[#64748b] leading-normal">
                    Pantau rekapitulasi data pra-pendaftaran perkara, verifikasi berkas, booking konsultasi, dan distribusi per kategori perkara.
                </p>
            </div>
        </div>

        <!-- 2. Secondary Navigation Tabs for Detail Reports -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 shadow-sm space-y-3">
            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">
                Laporan Detail Terperinci
            </span>
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="inline-flex items-center gap-2 bg-[#f8fafc] hover:bg-blue-50 border border-[#e2e8f0] hover:border-[#1e3a8a] text-[13px] font-semibold text-[#0f172a] hover:text-[#1e3a8a] px-4 py-2 rounded-[12px] transition shadow-xs">
                    <svg class="h-4 w-4 text-[#64748b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Pra-Pendaftaran</span>
                </a>
                <a href="{{ route('admin.laporan.verifikasi-berkas') }}" class="inline-flex items-center gap-2 bg-[#f8fafc] hover:bg-blue-50 border border-[#e2e8f0] hover:border-[#1e3a8a] text-[13px] font-semibold text-[#0f172a] hover:text-[#1e3a8a] px-4 py-2 rounded-[12px] transition shadow-xs">
                    <svg class="h-4 w-4 text-[#64748b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Verifikasi Berkas</span>
                </a>
                <a href="{{ route('admin.laporan.booking-konsultasi') }}" class="inline-flex items-center gap-2 bg-[#f8fafc] hover:bg-blue-50 border border-[#e2e8f0] hover:border-[#1e3a8a] text-[13px] font-semibold text-[#0f172a] hover:text-[#1e3a8a] px-4 py-2 rounded-[12px] transition shadow-xs">
                    <svg class="h-4 w-4 text-[#64748b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Booking Konsultasi</span>
                </a>
                <a href="{{ route('admin.laporan.reschedule-konsultasi') }}" class="inline-flex items-center gap-2 bg-[#f8fafc] hover:bg-blue-50 border border-[#e2e8f0] hover:border-[#1e3a8a] text-[13px] font-semibold text-[#0f172a] hover:text-[#1e3a8a] px-4 py-2 rounded-[12px] transition shadow-xs">
                    <svg class="h-4 w-4 text-[#64748b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Reschedule</span>
                </a>
                <a href="{{ route('admin.laporan.pengajuan-selesai') }}" class="inline-flex items-center gap-2 bg-[#f8fafc] hover:bg-blue-50 border border-[#e2e8f0] hover:border-[#1e3a8a] text-[13px] font-semibold text-[#0f172a] hover:text-[#1e3a8a] px-4 py-2 rounded-[12px] transition shadow-xs">
                    <svg class="h-4 w-4 text-[#64748b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Pengajuan Selesai</span>
                </a>
            </div>
        </div>

        <!-- 3. Filter Period Card -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    <div>
                        <label for="tanggal_mulai" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                            class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                            class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                    </div>
                </div>
                <div class="flex gap-2.5">
                    <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-[12px] h-11 px-5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Terapkan</span>
                    </button>
                    @if (request('tanggal_mulai') || request('tanggal_selesai'))
                        <a href="{{ route('admin.laporan.index') }}" class="bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#334155] rounded-[12px] h-11 px-5 text-[13px] font-semibold transition shadow-sm inline-flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- 4. Metric Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Total Pengajuan -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase">Total Pengajuan</span>
                    <div class="bg-[#f5f3ff] text-[#6d28d9] h-9 w-9 rounded-[10px] flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-[28px] font-extrabold text-[#0f172a] leading-tight">{{ $totalPengajuan }}</div>
                    <div class="text-[11px] text-[#64748b] mt-0.5">Periode terpilih</div>
                </div>
            </div>

            <!-- Card 2: Berkas Lengkap -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase">Berkas Lengkap</span>
                    <div class="bg-[#fffbeb] text-[#d97706] h-9 w-9 rounded-[10px] flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-[28px] font-extrabold text-[#0f172a] leading-tight">{{ $berkasLengkap }}</div>
                    <div class="text-[11px] text-[#64748b] mt-0.5">Siap konsultasi</div>
                </div>
            </div>

            <!-- Card 3: Konsultasi Selesai -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase">Konsultasi Selesai</span>
                    <div class="bg-[#eff6ff] text-[#1d4ed8] h-9 w-9 rounded-[10px] flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-[28px] font-extrabold text-[#0f172a] leading-tight">{{ $bookingSelesai }}</div>
                    <div class="text-[11px] text-[#64748b] mt-0.5">Sesi konsultasi tuntas</div>
                </div>
            </div>

            <!-- Card 4: Pengajuan Selesai -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase">Pengajuan Selesai</span>
                    <div class="bg-[#ecfdf5] text-[#059669] h-9 w-9 rounded-[10px] flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-[28px] font-extrabold text-[#0f172a] leading-tight">{{ $pengajuanSelesai }}</div>
                    <div class="text-[11px] text-[#64748b] mt-0.5">Perkara tuntas</div>
                </div>
            </div>
        </div>

        <!-- 5. Summary by Category Card -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] shadow-sm overflow-hidden">
            <div class="p-6 border-b border-[#f1f5f9]">
                <h3 class="font-bold text-[#0f172a] text-[16px]">Ringkasan per Kategori</h3>
                <p class="text-[13px] text-[#64748b] mt-1">Distribusi seluruh pengajuan perkara berdasarkan kategori perkara</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#e2e8f0]">
                    <thead class="bg-[#f8fafc]">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Kategori</th>
                            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Total</th>
                            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Berkas Lengkap</th>
                            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Jadwal Dipilih</th>
                            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Selesai</th>
                            <th class="px-6 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#e2e8f0]/60">
                        @forelse ($kategoriSummary as $summary)
                            <tr class="hover:bg-[#f8fafc] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-[13px] font-semibold text-[#0f172a]">
                                    {{ $summary->nama_kategori }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[13px] font-bold text-[#1e3a8a]">
                                    {{ $summary->total_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[13px] text-[#334155]">
                                    {{ $summary->berkas_lengkap_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[13px] text-[#334155]">
                                    {{ $summary->jadwal_dipilih_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-[13px] text-[#334155]">
                                    {{ $summary->selesai_count }}
                                </td>
                                <td class="px-6 py-4 text-[12px] text-[#64748b] italic max-w-xs truncate">
                                    {{ $summary->deskripsi ?: 'Tidak ada deskripsi.' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <x-empty-state title="Belum Ada Kategori Perkara" message="Belum ada data kategori perkara." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
