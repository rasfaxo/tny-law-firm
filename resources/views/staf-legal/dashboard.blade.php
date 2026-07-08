<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-[4px] text-sm text-[#94a3b8]">
            <span>Staf Legal</span>
            <svg class="h-[12px] w-[12px] text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-[#475569] font-medium">Dashboard</span>
        </div>
    </x-slot>

    @php
        $totalPengajuan = ($statistics['Pengajuan Menunggu Verifikasi'] ?? 0)
                        + ($statistics['Pengajuan Menunggu Verifikasi Ulang'] ?? 0)
                        + ($statistics['Pengajuan Berkas Lengkap'] ?? 0)
                        + ($statistics['Pengajuan Berkas Tidak Lengkap'] ?? 0);

        $menungguVerifikasi = ($statistics['Pengajuan Menunggu Verifikasi'] ?? 0)
                            + ($statistics['Pengajuan Menunggu Verifikasi Ulang'] ?? 0);

        $perluPerbaikan = $statistics['Pengajuan Berkas Tidak Lengkap'] ?? 0;
        $berkasLengkap = $statistics['Pengajuan Berkas Lengkap'] ?? 0;
    @endphp

    <div class="space-y-6">
        <!-- Banner Staf Legal -->
        <div class="rounded-[16px] p-[28px] text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-[0px_4px_12px_rgba(180,83,9,0.15)]" 
             style="background: linear-gradient(135deg, #92400e 0%, #d97706 100%)">
            <div class="max-w-[560px]">
                <h1 class="font-bold text-[20px] leading-tight">Selamat bekerja, Staf Legal</h1>
                <p class="text-[14px] text-white/80 mt-2">
                    Pantau pengajuan yang membutuhkan verifikasi dan tindak lanjuti catatan dokumen secara terstruktur.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('staf-legal.verifikasi-berkas.index') }}" 
                   class="bg-white text-[#92400e] font-bold text-[13px] tracking-[0.325px] h-[42px] px-[20px] rounded-[14px] flex items-center justify-center gap-2 shadow-sm hover:bg-amber-50 transition duration-150">
                    <svg class="h-[16px] w-[16px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lihat Pengajuan Verifikasi
                </a>
            </div>
        </div>

        <!-- Statistics Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Pengajuan Masuk -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 flex flex-col justify-between shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] h-[168px]">
                <div class="bg-[#ede9fe] text-purple-700 w-9 h-9 rounded-[14px] flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-[30px] font-bold text-[#0f172a] leading-none mt-2">{{ $totalPengajuan }}</div>
                    <div class="text-[13px] font-semibold text-[#334155] mt-1">Total Pengajuan Masuk</div>
                    <div class="text-[11px] text-[#94a3b8] mt-0.5">Seluruh pengajuan</div>
                </div>
            </div>

            <!-- Menunggu Verifikasi -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 flex flex-col justify-between shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] h-[168px]">
                <div class="bg-[#fef9c3] text-[#a16207] w-9 h-9 rounded-[14px] flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-[30px] font-bold text-[#0f172a] leading-none mt-2">{{ $menungguVerifikasi }}</div>
                    <div class="text-[13px] font-semibold text-[#334155] mt-1">Menunggu Verifikasi</div>
                    <div class="text-[11px] text-[#94a3b8] mt-0.5">Perlu diperiksa</div>
                </div>
            </div>

            <!-- Perlu Perbaikan -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 flex flex-col justify-between shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] h-[168px]">
                <div class="bg-[#fee2e2] text-red-700 w-9 h-9 rounded-[14px] flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-[30px] font-bold text-[#0f172a] leading-none mt-2">{{ $perluPerbaikan }}</div>
                    <div class="text-[13px] font-semibold text-[#334155] mt-1">Perlu Perbaikan</div>
                    <div class="text-[11px] text-[#94a3b8] mt-0.5">Catatan telah diberikan</div>
                </div>
            </div>

            <!-- Berkas Lengkap -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 flex flex-col justify-between shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] h-[168px]">
                <div class="bg-[#dcfce7] text-[#15803d] w-9 h-9 rounded-[14px] flex items-center justify-center">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-[30px] font-bold text-[#0f172a] leading-none mt-2">{{ $berkasLengkap }}</div>
                    <div class="text-[13px] font-semibold text-[#334155] mt-1">Berkas Lengkap</div>
                    <div class="text-[11px] text-[#94a3b8] mt-0.5">Siap konsultasi</div>
                </div>
            </div>
        </div>

        <!-- Pengajuan Terbaru Section -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] overflow-hidden">
            <div class="px-[20px] py-[16px] border-b border-[#f1f5f9]">
                <h3 class="font-bold text-[14px] text-[#0f172a]">Pengajuan Terbaru</h3>
                <p class="text-[12px] text-[#94a3b8] mt-0.5">{{ count($pengajuanPerluVerifikasi) }} pengajuan memerlukan tindakan</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#f8fafc]">
                        <tr>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Kode</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Klien</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Judul</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Kategori</th>
                            <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Status</th>
                            <th class="px-5 py-4 text-right text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#f1f5f9]">
                        @forelse ($pengajuanPerluVerifikasi as $pengajuan)
                            <tr>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-mono font-medium text-[#1e3a8a]">
                                    PP-{{ sprintf('%03d', $pengajuan->id_pendaftaran) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-medium text-[#334155]">
                                    {{ $pengajuan->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-[13px] text-[#334155]">
                                    {{ $pengajuan->judul_perkara }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-[12px] font-medium bg-[#f1f5f9] text-[#64748b]">
                                        {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-[12px] font-semibold">
                                    <a href="{{ route('staf-legal.verifikasi-berkas.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-[#1d4ed8] hover:text-[#1e40af] transition duration-150">
                                        Verifikasi
                                        <svg class="h-[11px] w-[11px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-[13px] text-[#64748b]">
                                    Tidak ada pengajuan yang memerlukan tindakan verifikasi saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
