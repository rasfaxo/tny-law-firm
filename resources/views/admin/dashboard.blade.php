<x-app-layout title="Dashboard Admin" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Dashboard']]">

    @php
        $berkasTidakLengkap = \App\Models\PraPendaftaranPerkara::where('status_pengajuan', 'berkas_tidak_lengkap')->count();
    @endphp

    <div class="space-y-6">
        <!-- Welcome Banner (Figma Node 79:1026) -->
        <div class="bg-gradient-to-r from-[#1e3a8a] via-[#1c357d] to-[#6d28d9] text-white p-6 sm:p-8 rounded-2xl shadow-lg flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
            <!-- Decorative circle overlay -->
            <div class="absolute right-0 top-0 w-64 h-64 bg-white/5 rounded-full -mr-20 -mt-20 pointer-events-none"></div>
            
            <div class="space-y-2 max-w-2xl relative z-10">
                <div class="bg-white/15 px-3 py-1 rounded-full text-xs font-extrabold tracking-wider uppercase inline-block">
                    Admin Panel
                </div>
                <h3 class="font-extrabold text-xl sm:text-2xl tracking-tight">Selamat datang, {{ Auth::user()->nama }}</h3>
                <p class="text-sm text-white/70 leading-relaxed">
                    Pantau aktivitas sistem, data pra-pendaftaran, staf legal, kategori perkara, dan jadwal konsultasi.
                </p>
            </div>
            <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="bg-[#d4af37] hover:bg-[#c5a02e] hover:shadow-lg transition duration-200 px-5 py-3 rounded-xl font-extrabold text-xs text-navy-dark tracking-wider uppercase flex items-center gap-2 shrink-0 z-10 shadow-md shadow-[#d4af37]/20">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Lihat Data Pra-Pendaftaran</span>
            </a>
        </div>

        <!-- 4 Stats Cards Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Klien -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-[#F5F3FF] p-2.5 rounded-xl text-[#6D28D9]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $statistics['Total Klien'] }}</span>
                </div>
                <div>
                    <span class="block text-sm font-bold text-navy-dark">Total Klien</span>
                    <span class="block text-xs text-gray-400">Akun klien aktif</span>
                </div>
            </x-card>

            <!-- Total Staf Legal -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-[#EBF5FF] p-2.5 rounded-xl text-[#1E3A8A]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $statistics['Total Staf Legal'] }}</span>
                </div>
                <div>
                    <span class="block text-sm font-bold text-navy-dark">Total Staf Legal</span>
                    <span class="block text-xs text-gray-400">Akun staf legal</span>
                </div>
            </x-card>

            <!-- Pra-Pendaftaran -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-[#FFFBEB] p-2.5 rounded-xl text-[#D97706]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $statistics['Total Pengajuan'] }}</span>
                </div>
                <div>
                    <span class="block text-sm font-bold text-navy-dark">Pra-Pendaftaran</span>
                    <span class="block text-xs text-gray-400">Seluruh data pengajuan</span>
                </div>
            </x-card>

            <!-- Jadwal Aktif -->
            <x-card class="flex flex-col justify-between h-[160px]">
                <div class="flex items-center justify-between">
                    <div class="bg-[#F0FDF4] p-2.5 rounded-xl text-[#15803D]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <span class="text-3xl font-extrabold text-navy-dark tracking-tight">{{ $statistics['Jadwal Tersedia'] }}</span>
                </div>
                <div>
                    <span class="block text-sm font-bold text-navy-dark">Jadwal Aktif</span>
                    <span class="block text-xs text-gray-400">Slot jadwal tersedia</span>
                </div>
            </x-card>
        </div>

        <!-- Two Columns Layout (Figma Node 79:1099) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Left Card: Ringkasan Status Pengajuan -->
            <x-card class="lg:col-span-5 space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Ringkasan Status Pengajuan</h3>
                    <p class="text-xs text-gray-400 mt-1">Distribusi status utama pada sistem.</p>
                </div>
                
                <div class="space-y-3">
                    <!-- Menunggu Verifikasi -->
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 flex justify-between items-center">
                        <span class="bg-[#FEF9C3] border border-[#FDE68A] text-[#A16207] text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            Menunggu Verifikasi
                        </span>
                        <span class="text-lg font-extrabold text-navy-dark">{{ $statistics['Pengajuan Menunggu Verifikasi'] }}</span>
                    </div>

                    <!-- Berkas Tidak Lengkap -->
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 flex justify-between items-center">
                        <span class="bg-[#FEE2E2] border border-[#FECACA] text-[#B91C1C] text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            Berkas Tidak Lengkap
                        </span>
                        <span class="text-lg font-extrabold text-navy-dark">{{ $berkasTidakLengkap }}</span>
                    </div>

                    <!-- Berkas Lengkap -->
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 flex justify-between items-center">
                        <span class="bg-[#DCFCE7] border border-[#BBF7D0] text-[#15803D] text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            Berkas Lengkap
                        </span>
                        <span class="text-lg font-extrabold text-navy-dark">{{ $statistics['Pengajuan Berkas Lengkap'] }}</span>
                    </div>

                    <!-- Jadwal Dipilih -->
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 flex justify-between items-center">
                        <span class="bg-[#DBEAFE] border border-[#BFDBFE] text-[#1D4ED8] text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">
                            Jadwal Dipilih
                        </span>
                        <span class="text-lg font-extrabold text-navy-dark">{{ $statistics['Pengajuan Jadwal Dipilih'] }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Right Card: Pra-Pendaftaran Terbaru -->
            <x-card class="lg:col-span-7 flex flex-col justify-between overflow-hidden">
                <div class="space-y-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-navy-dark text-lg">Pra-Pendaftaran Terbaru</h3>
                            <p class="text-xs text-gray-400 mt-1">3 pengajuan terbaru dari klien.</p>
                        </div>
                        <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="text-xs font-bold text-accent-blue hover:underline">
                            Lihat Semua &rarr;
                        </a>
                    </div>

                    <div class="overflow-x-auto border border-[#E2E8F0] rounded-xl">
                        <table class="min-w-full divide-y divide-[#E2E8F0]">
                            <thead class="bg-[#F8FAFC]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kode</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Klien</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#E2E8F0]">
                                @forelse ($pengajuanTerbaru->take(3) as $pengajuan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-[#1E3A8A] font-mono">
                                            PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-navy-dark">
                                            {{ $pengajuan->klien?->nama ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-status-badge :status="$pengajuan->status_pengajuan" />
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline transition">
                                                <span>Detail</span>
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-400">
                                            Belum ada pengajuan terbaru.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
