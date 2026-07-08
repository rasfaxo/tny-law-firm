<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-[4px] text-sm text-[#94a3b8]">
            <span>Staf Legal</span>
            <svg class="h-[12px] w-[12px] text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-[#475569] font-medium">Pengajuan Verifikasi</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-700 shadow-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-700 shadow-sm border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filter Bar -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)]">
            <form method="GET" action="{{ route('staf-legal.verifikasi-berkas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <!-- Cari -->
                <div class="space-y-2">
                    <label for="search" class="block text-[11px] font-semibold text-[#374151] tracking-[0.3px] uppercase">Cari</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Cari kode, klien, atau judul…" 
                           class="w-full bg-white border border-[#e2e8f0] focus:border-[#1d4ed8] focus:ring-[#1d4ed8] h-[44px] rounded-[14px] px-[17px] text-[14px] text-[#334155] placeholder-[#94a3b8] transition duration-150">
                </div>

                <!-- Filter Status -->
                <div class="space-y-2">
                    <label for="status" class="block text-[11px] font-semibold text-[#374151] tracking-[0.3px] uppercase">Filter Status</label>
                    <select name="status" id="status" 
                            class="w-full bg-white border border-[#e2e8f0] focus:border-[#1d4ed8] focus:ring-[#1d4ed8] h-[44px] rounded-[14px] px-[17px] text-[14px] text-[#334155] transition duration-150">
                        <option value="">Semua Status</option>
                        <option value="menunggu_verifikasi" @selected(request('status') === 'menunggu_verifikasi')>Menunggu Verifikasi</option>
                        <option value="menunggu_verifikasi_ulang" @selected(request('status') === 'menunggu_verifikasi_ulang')>Verifikasi Ulang</option>
                        <option value="berkas_tidak_lengkap" @selected(request('status') === 'berkas_tidak_lengkap')>Berkas Tidak Lengkap</option>
                        <option value="berkas_lengkap" @selected(request('status') === 'berkas_lengkap')>Berkas Lengkap</option>
                        <option value="jadwal_dipilih" @selected(request('status') === 'jadwal_dipilih')>Jadwal Dipilih</option>
                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    </select>
                </div>

                <!-- Filter Kategori -->
                <div class="space-y-2">
                    <label for="kategori" class="block text-[11px] font-semibold text-[#374151] tracking-[0.3px] uppercase">Filter Kategori</label>
                    <select name="kategori" id="kategori" 
                            class="w-full bg-white border border-[#e2e8f0] focus:border-[#1d4ed8] focus:ring-[#1d4ed8] h-[44px] rounded-[14px] px-[17px] text-[14px] text-[#334155] transition duration-150">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriList as $kategori)
                            <option value="{{ $kategori->id_kategori }}" @selected(request('kategori') == $kategori->id_kategori)>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Terapkan Button -->
                <div>
                    <button type="submit" 
                            class="w-full bg-[#1e3a8a] text-white font-semibold text-[13px] tracking-[0.325px] h-[44px] px-[20px] rounded-[14px] flex items-center justify-center shadow-md hover:bg-[#1e40af] transition duration-150 cursor-pointer">
                        Terapkan
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] overflow-hidden">
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
                        @forelse ($pengajuan as $item)
                            @php
                                $isVerifiable = in_array($item->status_pengajuan, ['menunggu_verifikasi', 'menunggu_verifikasi_ulang']);
                            @endphp
                            <tr>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-mono font-medium text-[#1e3a8a]">
                                    PP-{{ sprintf('%03d', $item->id_pendaftaran) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-medium text-[#334155]">
                                    {{ $item->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-[13px] text-[#334155]">
                                    {{ $item->judul_perkara }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-[10px] text-[12px] font-medium bg-[#f1f5f9] text-[#64748b]">
                                        {{ $item->kategori?->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$item->status_pengajuan" />
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-[12px] font-semibold">
                                    @if ($isVerifiable)
                                        <a href="{{ route('staf-legal.verifikasi-berkas.show', $item) }}" class="inline-flex items-center gap-1 text-[#1d4ed8] hover:text-[#1e40af] transition duration-150">
                                            Verifikasi
                                            <svg class="h-[11px] w-[11px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('staf-legal.verifikasi-berkas.show', $item) }}" class="inline-flex items-center gap-1 text-[#475569] hover:text-[#0f172a] transition duration-150">
                                            Detail
                                            <svg class="h-[11px] w-[11px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-[13px] text-[#64748b]">
                                    Tidak ada data pengajuan verifikasi yang cocok dengan filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Styled Pagination -->
            @if ($pengajuan->hasPages())
                <div class="px-5 py-4 bg-white border-t border-[#f1f5f9]">
                    {{ $pengajuan->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
