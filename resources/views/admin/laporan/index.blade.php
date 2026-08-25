<x-app-layout title="Laporan & Rekapitulasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Laporan']]">

    <div class="space-y-5">
        <!-- 1. Hero Header Card -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="space-y-1.5">
                <h2 class="text-xl font-bold text-navy-dark leading-snug">
                    Laporan & Rekapitulasi Sistem
                </h2>
                <p class="text-sm text-gray-500 leading-normal">
                    Pantau rekapitulasi data pra-pendaftaran perkara, verifikasi berkas, booking konsultasi, dan distribusi per kategori perkara.
                </p>
            </div>
            <a href="{{ route('admin.laporan.index.cetak', request()->query()) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl h-11 px-5 text-sm font-semibold transition shadow-sm inline-flex items-center gap-2 shrink-0">
                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                <span>Cetak Rekapitulasi</span>
            </a>
        </div>

        <!-- 2. Secondary Navigation Tabs for Detail Reports -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm space-y-3">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">
                Laporan Detail Terperinci
            </span>
            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="inline-flex items-center gap-2 bg-[#F8FAFC] hover:bg-blue-50 border border-[#E2E8F0] hover:border-accent-blue text-sm font-semibold text-navy-dark hover:text-accent-blue px-4 py-2 rounded-xl transition shadow-xs">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Pra-Pendaftaran</span>
                </a>
                <a href="{{ route('admin.laporan.verifikasi-berkas') }}" class="inline-flex items-center gap-2 bg-[#F8FAFC] hover:bg-blue-50 border border-[#E2E8F0] hover:border-accent-blue text-sm font-semibold text-navy-dark hover:text-accent-blue px-4 py-2 rounded-xl transition shadow-xs">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Verifikasi Berkas</span>
                </a>
                <a href="{{ route('admin.laporan.booking-konsultasi') }}" class="inline-flex items-center gap-2 bg-[#F8FAFC] hover:bg-blue-50 border border-[#E2E8F0] hover:border-accent-blue text-sm font-semibold text-navy-dark hover:text-accent-blue px-4 py-2 rounded-xl transition shadow-xs">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Booking Konsultasi</span>
                </a>
                <a href="{{ route('admin.laporan.reschedule-konsultasi') }}" class="inline-flex items-center gap-2 bg-[#F8FAFC] hover:bg-blue-50 border border-[#E2E8F0] hover:border-accent-blue text-sm font-semibold text-navy-dark hover:text-accent-blue px-4 py-2 rounded-xl transition shadow-xs">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Reschedule</span>
                </a>
                <a href="{{ route('admin.laporan.pengajuan-selesai') }}" class="inline-flex items-center gap-2 bg-[#F8FAFC] hover:bg-blue-50 border border-[#E2E8F0] hover:border-accent-blue text-sm font-semibold text-navy-dark hover:text-accent-blue px-4 py-2 rounded-xl transition shadow-xs">
                    <svg class="h-4 w-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span>Pengajuan Selesai</span>
                </a>
            </div>
        </div>

        <!-- 3. Filter Period Card -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl p-6 shadow-sm">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="tanggal_mulai" :value="__('Tanggal Mulai')" />
                        <x-text-input type="date" id="tanggal_mulai" name="tanggal_mulai" :value="request('tanggal_mulai')" class="w-full text-navy-dark" />
                    </div>
                    <div>
                        <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai')" />
                        <x-text-input type="date" id="tanggal_selesai" name="tanggal_selesai" :value="request('tanggal_selesai')" class="w-full text-navy-dark" />
                    </div>
                </div>
                <div class="flex gap-2.5">
                    <x-primary-button type="submit" class="gap-2">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                        </svg>
                        <span>Terapkan</span>
                    </x-primary-button>
                    @if (request('tanggal_mulai') || request('tanggal_selesai'))
                        <x-secondary-button href="{{ route('admin.laporan.index') }}" tag="a">
                            Reset
                        </x-secondary-button>
                    @endif
                    <a href="{{ route('admin.laporan.index.cetak', request()->query()) }}" target="_blank" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl h-11 px-5 text-sm font-semibold transition shadow-sm inline-flex items-center gap-2 ml-auto">
                        <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak Rekapitulasi</span>
                    </a>
                </div>
            </form>
        </div>

        <!-- 4. Metric Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Total Pengajuan -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pengajuan</span>
                    <div class="bg-[#F5F3FF] text-[#6D28D9] h-9 w-9 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $totalPengajuan }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Periode terpilih</div>
                </div>
            </div>

            <!-- Card 2: Berkas Lengkap -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Berkas Lengkap</span>
                    <div class="bg-[#FFFBEB] text-[#D97706] h-9 w-9 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $berkasLengkap }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Siap konsultasi</div>
                </div>
            </div>

            <!-- Card 3: Konsultasi Selesai -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Konsultasi Selesai</span>
                    <div class="bg-[#EFF6FF] text-[#1D4ED8] h-9 w-9 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $bookingSelesai }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Sesi konsultasi tuntas</div>
                </div>
            </div>

            <!-- Card 4: Pengajuan Selesai -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl p-5 shadow-sm flex flex-col justify-between h-36">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pengajuan Selesai</span>
                    <div class="bg-[#ECFDF5] text-[#059669] h-9 w-9 rounded-lg flex items-center justify-center shrink-0">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $pengajuanSelesai }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">Perkara tuntas</div>
                </div>
            </div>
        </div>

        <!-- 5. Summary by Category Card -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-[#F1F5F9]">
                <h3 class="font-bold text-navy-dark text-lg">Ringkasan per Kategori</h3>
                <p class="text-sm text-gray-500 mt-1">Distribusi seluruh pengajuan perkara berdasarkan kategori perkara</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Berkas Lengkap</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal Dipilih</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($kategoriSummary as $summary)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-navy-dark">
                                    {{ $summary->nama_kategori }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-accent-blue">
                                    {{ $summary->total_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $summary->berkas_lengkap_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $summary->jadwal_dipilih_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                    {{ $summary->selesai_count }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 italic max-w-xs truncate">
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
