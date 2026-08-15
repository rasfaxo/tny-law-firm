<x-app-layout title="Laporan Pengajuan Selesai" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Laporan', 'url' => route('admin.laporan.index')], ['label' => 'Pengajuan Selesai']]">

    <style>
        @media print {
            nav, header, .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-area { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }
    </style>

    <div class="space-y-5">
        <!-- 1. Hero Header Card (no-print) -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4 no-print">
            <div class="space-y-1.5">
                <h2 class="text-[18px] font-bold text-[#0f172a] leading-snug">
                    Laporan Pengajuan Selesai
                </h2>
                <p class="text-[13px] text-[#64748b] leading-normal">
                    Rekapitulasi perkara yang telah selesai melalui seluruh rangkaian pra-pendaftaran dan konsultasi.
                </p>
            </div>
            <a href="{{ route('admin.laporan.index') }}" class="bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#334155] rounded-[14px] px-5 py-2.5 text-[13px] font-semibold inline-flex items-center gap-2 transition shadow-sm shrink-0">
                <span>← Kembali ke Rekapitulasi</span>
            </a>
        </div>

        <!-- 2. Navigation Pill Tabs for Detail Reports (no-print) -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-5 shadow-sm space-y-3 no-print">
            <span class="text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase block">
                Pilih Detail Laporan
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
                <a href="{{ route('admin.laporan.pengajuan-selesai') }}" class="inline-flex items-center gap-2 bg-[#1e3a8a] text-white border border-[#1e3a8a] text-[13px] font-semibold px-4 py-2 rounded-[12px] transition shadow-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Pengajuan Selesai</span>
                </a>
            </div>
        </div>

        <!-- 3. Filter Card (no-print) -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-sm no-print">
            <form method="GET" action="{{ route('admin.laporan.pengajuan-selesai') }}" class="space-y-5">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label for="tanggal_mulai" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Tanggal Awal Selesai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" 
                            class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Tanggal Akhir Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" 
                            class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                    </div>
                    <div>
                        <label for="id_kategori" class="block text-[10px] font-semibold text-[#64748b] tracking-[0.25px] uppercase mb-2">Kategori Perkara</label>
                        <select id="id_kategori" name="id_kategori" 
                            class="w-full bg-[#f8fafc] border border-[#e2e8f0] focus:border-[#1e3a8a] focus:ring focus:ring-[#1e3a8a]/20 rounded-[12px] text-[13px] transition shadow-sm h-11 px-4 text-[#0f172a]">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriPerkara as $kategori)
                                <option value="{{ $kategori->id_kategori }}" @selected((string) ($filters['id_kategori'] ?? '') === (string) $kategori->id_kategori)>{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-[#e2e8f0]">
                    <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white rounded-[12px] h-11 px-5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>{{ __('Terapkan Filter') }}</span>
                    </button>
                    <a href="{{ route('admin.laporan.pengajuan-selesai') }}" class="bg-white border border-[#e2e8f0] hover:bg-gray-50 text-[#334155] rounded-[12px] h-11 px-5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2">
                        <svg class="h-4 w-4 shrink-0 text-[#64748b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span>{{ __('Reset Filter') }}</span>
                    </a>
                    <a href="{{ route('admin.laporan.pengajuan-selesai.cetak', request()->query()) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-[12px] h-11 px-5 text-[13px] font-semibold transition shadow-sm inline-flex items-center gap-2 ml-auto">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>{{ __('Cetak Laporan') }}</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Table Card (Print Area) -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] shadow-sm overflow-hidden print-area">
            <div class="p-6 border-b border-[#f1f5f9] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-bold text-[#0f172a] text-[16px]">{{ __('Laporan Pengajuan Selesai') }}</h3>
                    <p class="text-[13px] text-[#64748b] mt-0.5">Total data ditemukan: <span class="font-bold text-[#0f172a]">{{ $laporan->count() }}</span></p>
                </div>
                <div class="text-[12px] font-semibold text-[#475569] bg-[#f8fafc] border border-[#e2e8f0] px-4 py-2 rounded-[12px]">
                    @if (($filters['tanggal_mulai'] ?? null) || ($filters['tanggal_selesai'] ?? null))
                        Periode: {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d M Y') }}
                    @else
                        Periode: Semua Data
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#e2e8f0]">
                    <thead class="bg-[#f8fafc]">
                        <tr>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">No</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Kode</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Nama Klien</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Kategori</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Judul Perkara</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Tanggal Pengajuan</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Tanggal Selesai</th>
                            <th class="px-5 py-3.5 text-left text-[10px] font-semibold text-[#64748b] uppercase tracking-[0.25px]">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#e2e8f0]/60">
                        @forelse ($laporan as $pengajuan)
                            @php
                                $tanggalSelesai = $pengajuan->riwayatStatus->first()?->created_at;
                            @endphp
                            <tr class="hover:bg-[#f8fafc] transition duration-150">
                                <td class="px-5 py-4 whitespace-nowrap text-[12px] text-gray-400 font-semibold">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-bold text-[#1e3a8a] font-mono">
                                    PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-semibold text-[#0f172a]">
                                    {{ $pengajuan->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] text-[#334155]">
                                    {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-[13px] text-[#334155] truncate max-w-xs font-medium">
                                    {{ $pengajuan->judul_perkara }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[12px] text-[#64748b]">
                                    {{ $pengajuan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[12px] text-[#64748b]">
                                    {{ $tanggalSelesai?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-12 text-center">
                                    <x-empty-state title="Tidak Ada Data" message="Tidak ada data pengajuan selesai sesuai filter yang dipilih." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
