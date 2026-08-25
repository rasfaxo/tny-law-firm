<x-app-layout title="Dashboard Staf Legal" :breadcrumbs="[['label' => 'Staf Legal'], ['label' => 'Dashboard']]">

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
        <div class="rounded-2xl p-7 text-white flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-md shadow-amber-900/10 bg-gradient-to-r from-amber-800 to-amber-600">
            <div class="max-w-xl">
                <h1 class="font-extrabold text-2xl leading-tight">Selamat bekerja, Staf Legal</h1>
                <p class="text-sm text-white/80 mt-2">
                    Pantau pengajuan yang membutuhkan verifikasi dan tindak lanjuti catatan dokumen secara terstruktur.
                </p>
            </div>
            <div class="shrink-0">
                <a href="{{ route('staf-legal.verifikasi-berkas.index') }}" 
                   class="bg-white text-amber-800 font-bold text-sm h-11 px-5 rounded-xl flex items-center justify-center gap-2 shadow-sm hover:bg-amber-50 transition duration-150">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Lihat Pengajuan Verifikasi
                </a>
            </div>
        </div>

        <!-- Statistics Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Pengajuan Masuk -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-[#F5F3FF] p-2.5 rounded-xl text-[#6D28D9]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $totalPengajuan }}</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-navy-dark">Total Pengajuan Masuk</span>
                    <span class="block text-xs text-gray-400">Seluruh data pengajuan</span>
                </div>
            </x-card>

            <!-- Menunggu Verifikasi -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-yellow-50 p-2.5 rounded-xl text-yellow-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $menungguVerifikasi }}</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-navy-dark">Menunggu Verifikasi</span>
                    <span class="block text-xs text-gray-400">Perlu diperiksa</span>
                </div>
            </x-card>

            <!-- Perlu Perbaikan -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-red-50 p-2.5 rounded-xl text-red-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $perluPerbaikan }}</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-navy-dark">Perlu Perbaikan</span>
                    <span class="block text-xs text-gray-400">Catatan telah diberikan</span>
                </div>
            </x-card>

            <!-- Berkas Lengkap -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-green-50 p-2.5 rounded-xl text-green-600">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $berkasLengkap }}</span>
                </div>
                <div class="space-y-0.5">
                    <span class="block text-sm font-bold text-navy-dark">Berkas Lengkap</span>
                    <span class="block text-xs text-gray-400">Siap konsultasi</span>
                </div>
            </x-card>
        </div>

        <!-- Pengajuan Terbaru Section -->
        <x-card class="p-0 overflow-hidden">
            <div class="px-6 py-4 border-b border-[#F1F5F9]">
                <h3 class="font-bold text-navy-dark text-base">Pengajuan Terbaru</h3>
                <p class="text-xs text-gray-500 mt-1">{{ count($pengajuanPerluVerifikasi) }} pengajuan memerlukan tindakan</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Klien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Judul</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 tracking-wider uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($pengajuanPerluVerifikasi as $pengajuan)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-accent-blue">
                                    PP-{{ sprintf('%03d', $pengajuan->id_pendaftaran) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-navy-dark">
                                    {{ $pengajuan->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                    {{ $pengajuan->judul_perkara }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                    {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                    <a href="{{ route('staf-legal.verifikasi-berkas.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-accent-blue hover:underline transition">
                                        <span>Verifikasi</span>
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    <x-empty-state title="Tidak ada pengajuan" message="Tidak ada pengajuan yang memerlukan tindakan verifikasi saat ini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</x-app-layout>
