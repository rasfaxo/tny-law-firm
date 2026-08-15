<x-app-layout title="Pengajuan Verifikasi" :breadcrumbs="[['label' => 'Staf Legal'], ['label' => 'Pengajuan Verifikasi']]">

    <div class="space-y-6">
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

        <!-- Filter Bar -->
        <!-- Filter Bar -->
        <x-card>
            <form method="GET" action="{{ route('staf-legal.verifikasi-berkas.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <!-- Cari -->
                <div class="space-y-2">
                    <x-input-label for="search" value="Cari" />
                    <x-text-input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari kode, klien, atau judul…" />
                </div>

                <!-- Filter Status -->
                <div class="space-y-2">
                    <x-input-label for="status" value="Filter Status" />
                    <select name="status" id="status" class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4 appearance-none">
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
                    <x-input-label for="kategori" value="Filter Kategori" />
                    <select name="kategori" id="kategori" class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4 appearance-none">
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
                    <x-primary-button class="w-full">
                        Terapkan
                    </x-primary-button>
                </div>
            </form>
        </x-card>

        <!-- Table Card -->
        <x-card class="p-0 overflow-hidden sm:p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F1F5F9]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Kode</th>
                            <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Klien</th>
                            <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Judul</th>
                            <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Kategori</th>
                            <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Status</th>
                            <th class="px-5 py-4 text-right text-[11px] font-bold text-gray-400 tracking-wider uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#F1F5F9]">
                        @forelse ($pengajuan as $item)
                            @php
                                $isVerifiable = in_array($item->status_pengajuan, ['menunggu_verifikasi', 'menunggu_verifikasi_ulang']);
                            @endphp
                            <tr class="hover:bg-gray-50/40 transition">
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-mono font-medium text-accent-blue">
                                    PP-{{ sprintf('%03d', $item->id_pendaftaran) }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-[13px] font-bold text-navy-dark">
                                    {{ $item->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-5 py-4 text-[13px] text-gray-600 font-medium">
                                    {{ $item->judul_perkara }}
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full border border-gray-200 text-[11px] font-bold bg-gray-100 text-gray-700">
                                        {{ $item->kategori?->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$item->status_pengajuan" />
                                </td>
                                <td class="px-5 py-4 whitespace-nowrap text-right text-sm">
                                    @if ($isVerifiable)
                                        <a href="{{ route('staf-legal.verifikasi-berkas.show', $item) }}" class="inline-flex items-center gap-1 text-accent-blue hover:text-blue-800 transition font-bold">
                                            Verifikasi
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @else
                                        <a href="{{ route('staf-legal.verifikasi-berkas.show', $item) }}" class="inline-flex items-center gap-1 text-gray-500 hover:text-gray-800 transition font-bold">
                                            Detail
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-0">
                                    <x-empty-state title="Tidak ada data" message="Tidak ada data pengajuan verifikasi yang cocok dengan filter." class="border-0 rounded-none bg-transparent" />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Custom Styled Pagination -->
            @if ($pengajuan->hasPages())
                <div class="px-5 py-4 bg-white border-t border-[#F1F5F9]">
                    {{ $pengajuan->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
