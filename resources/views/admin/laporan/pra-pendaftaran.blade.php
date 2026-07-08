<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1 no-print">
            <span>Admin</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-600">Data Pra-Pendaftaran</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight no-print">
            {{ __('Data Pra-Pendaftaran') }}
        </h2>
    </x-slot>

    <style>
        @media print {
            nav, header, .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-area { box-shadow: none !important; border: none !important; padding: 0 !important; }
        }
    </style>

    @php
        $statusOptions = [
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'berkas_tidak_lengkap' => 'Berkas Tidak Lengkap',
            'menunggu_verifikasi_ulang' => 'Menunggu Verifikasi Ulang',
            'berkas_lengkap' => 'Berkas Lengkap',
            'jadwal_dipilih' => 'Jadwal Dipilih',
            'selesai' => 'Selesai',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Filter Card -->
        <div class="no-print bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('admin.laporan.pra-pendaftaran') }}" class="space-y-6">
                <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="tanggal_mulai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Awal</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" 
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Akhir</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" 
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                    </div>
                    <div>
                        <label for="status_pengajuan" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Status Pengajuan</label>
                        <select id="status_pengajuan" name="status_pengajuan" 
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                            <option value="">Semua Status</option>
                            @foreach ($statusOptions as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status_pengajuan'] ?? '') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="id_kategori" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Kategori Perkara</label>
                        <select id="id_kategori" name="id_kategori" 
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriPerkara as $kategori)
                                <option value="{{ $kategori->id_kategori }}" @selected((string) ($filters['id_kategori'] ?? '') === (string) $kategori->id_kategori)>{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-3 pt-4 border-t border-[#E2E8F0]">
                    <button type="submit" class="inline-flex items-center justify-center bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>{{ __('Terapkan Filter') }}</span>
                    </button>
                    <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-sm gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H12v9l-7-7"></path>
                        </svg>
                        <span>{{ __('Reset Filter') }}</span>
                    </a>
                    <button type="button" onclick="window.print()" class="inline-flex items-center justify-center bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs px-5 py-2.5 rounded-xl transition shadow-md shadow-emerald-700/20 uppercase tracking-widest gap-1.5">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>{{ __('Cetak Laporan') }}</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="print-area bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-[#E2E8F0] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">{{ __('Laporan Pra-Pendaftaran Perkara') }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Total data ditemukan: <span class="font-bold text-navy-dark">{{ $laporan->count() }}</span></p>
                </div>
                <div class="text-xs font-semibold text-gray-500 bg-[#F8FAFC] border border-[#E2E8F0] px-4 py-2 rounded-xl">
                    @if (($filters['tanggal_mulai'] ?? null) || ($filters['tanggal_selesai'] ?? null))
                        Periode: {{ \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d M Y') }}
                    @else
                        Periode: Semua Data
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-4 py-3 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Kode</th>
                            <th class="px-4 py-3 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Klien</th>
                            <th class="px-4 py-3 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-4 py-3 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Judul Perkara</th>
                            <th class="px-4 py-3 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th class="px-4 py-3 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider no-print">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($laporan as $pengajuan)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-400 font-semibold">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-bold text-[#1E3A8A] font-mono">
                                    PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs font-semibold text-[#334155]">
                                    {{ $pengajuan->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-500">
                                    {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-gray-500 truncate max-w-xs">
                                    {{ $pengajuan->judul_perkara }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-xs text-gray-400">
                                    {{ $pengajuan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-right text-xs font-bold no-print">
                                    <a href="{{ route('admin.pra-pendaftaran.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-accent-blue hover:underline transition">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-xs text-gray-400">
                                    Tidak ada data pra-pendaftaran sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
