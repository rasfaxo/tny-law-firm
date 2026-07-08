<x-app-layout title="Laporan Admin" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Laporan']]">

    <div class="space-y-6">
        <!-- Secondary Navigation Tabs for Detail Reports -->
        <div class="bg-white border border-[#E2E8F0] p-4 rounded-2xl shadow-sm">
            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider block mb-3">Detail Laporan Terperinci</span>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="inline-flex items-center justify-center bg-[#F8FAFC] border border-[#E2E8F0] hover:border-accent-blue hover:bg-blue-50 text-xs font-semibold text-navy-dark px-4 py-2.5 rounded-xl transition">
                    {{ __('Pra-Pendaftaran') }}
                </a>
                <a href="{{ route('admin.laporan.verifikasi-berkas') }}" class="inline-flex items-center justify-center bg-[#F8FAFC] border border-[#E2E8F0] hover:border-accent-blue hover:bg-blue-50 text-xs font-semibold text-navy-dark px-4 py-2.5 rounded-xl transition">
                    {{ __('Verifikasi Berkas') }}
                </a>
                <a href="{{ route('admin.laporan.booking-konsultasi') }}" class="inline-flex items-center justify-center bg-[#F8FAFC] border border-[#E2E8F0] hover:border-accent-blue hover:bg-blue-50 text-xs font-semibold text-navy-dark px-4 py-2.5 rounded-xl transition">
                    {{ __('Booking Konsultasi') }}
                </a>
                <a href="{{ route('admin.laporan.reschedule-konsultasi') }}" class="inline-flex items-center justify-center bg-[#F8FAFC] border border-[#E2E8F0] hover:border-accent-blue hover:bg-blue-50 text-xs font-semibold text-navy-dark px-4 py-2.5 rounded-xl transition">
                    {{ __('Reschedule') }}
                </a>
                <a href="{{ route('admin.laporan.pengajuan-selesai') }}" class="inline-flex items-center justify-center bg-[#F8FAFC] border border-[#E2E8F0] hover:border-accent-blue hover:bg-blue-50 text-xs font-semibold text-navy-dark px-4 py-2.5 rounded-xl transition">
                    {{ __('Pengajuan Selesai') }}
                </a>
            </div>
        </div>

        <!-- Filter Period Card -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('admin.laporan.index') }}" class="flex flex-col md:flex-row md:items-end gap-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1">
                    <div>
                        <label for="tanggal_mulai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Mulai</label>
                        <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Tanggal Selesai</label>
                        <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ request('tanggal_selesai') }}"
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4">
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-5 py-3 rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest h-11">
                        Terapkan
                    </button>
                    @if (request('tanggal_mulai') || request('tanggal_selesai'))
                        <a href="{{ route('admin.laporan.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-rose-300 text-navy-dark hover:text-rose-600 font-bold text-xs px-4 py-3 rounded-xl transition shadow-sm h-11">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Metric Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1: Total Pengajuan -->
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm flex flex-col justify-between h-40">
                <div class="bg-violet-50 text-violet-700 h-10 w-10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $totalPengajuan }}</div>
                    <div class="text-xs font-semibold text-gray-500 mt-1">Total Pengajuan</div>
                    <div class="text-xxs text-gray-400 mt-0.5">Periode terpilih</div>
                </div>
            </div>

            <!-- Card 2: Pengajuan Selesai -->
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm flex flex-col justify-between h-40">
                <div class="bg-emerald-50 text-emerald-700 h-10 w-10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $pengajuanSelesai }}</div>
                    <div class="text-xs font-semibold text-gray-500 mt-1">Pengajuan Selesai</div>
                    <div class="text-xxs text-gray-400 mt-0.5">Telah selesai</div>
                </div>
            </div>

            <!-- Card 3: Berkas Lengkap -->
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm flex flex-col justify-between h-40">
                <div class="bg-amber-50 text-amber-700 h-10 w-10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $berkasLengkap }}</div>
                    <div class="text-xs font-semibold text-gray-500 mt-1">Berkas Lengkap</div>
                    <div class="text-xxs text-gray-400 mt-0.5">Siap konsultasi</div>
                </div>
            </div>

            <!-- Card 4: Konsultasi Selesai -->
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm flex flex-col justify-between h-40">
                <div class="bg-blue-50 text-blue-700 h-10 w-10 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <div class="text-3xl font-extrabold text-navy-dark leading-tight">{{ $bookingSelesai }}</div>
                    <div class="text-xs font-semibold text-gray-500 mt-1">Konsultasi Selesai</div>
                    <div class="text-xxs text-gray-400 mt-0.5">Booking selesai</div>
                </div>
            </div>
        </div>

        <!-- Summary by Category Card -->
        <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-[#E2E8F0]">
                <h3 class="font-bold text-navy-dark text-sm">Ringkasan per Kategori</h3>
                <p class="text-xxs text-gray-400 mt-0.5 font-semibold">Distribusi pengajuan berdasarkan kategori perkara</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Berkas Lengkap</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jadwal Dipilih</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Selesai</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($kategoriSummary as $summary)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-navy-dark">
                                    {{ $summary->nama_kategori }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-navy-dark">
                                    {{ $summary->total_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ $summary->berkas_lengkap_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ $summary->jadwal_dipilih_count }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                    {{ $summary->selesai_count }}
                                </td>
                                <td class="px-6 py-4 text-xxs text-gray-400 italic max-w-xs truncate">
                                    {{ $summary->deskripsi ?: 'Tidak ada deskripsi.' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-gray-400">
                                    Belum ada data kategori perkara.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
