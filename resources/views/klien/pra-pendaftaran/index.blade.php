<x-app-layout title="Daftar Pengajuan Perkara" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Daftar Pengajuan']]">

    <div class="space-y-6">
        <div class="flex justify-end">
            <!-- CTA Buat Pengajuan -->
            <a href="{{ route('klien.pra-pendaftaran.create') }}" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs tracking-wider uppercase px-5 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Pengajuan</span>
            </a>
        </div>
        <!-- Filter & Search Bar -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('klien.pra-pendaftaran.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search Input -->
                <div class="md:col-span-6 space-y-1.5">
                    <label for="search" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Cari Pengajuan</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Judul atau kode pengajuan..." 
                               class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 pl-4 pr-10 py-2">
                        @if(request('search'))
                            <a href="{{ route('klien.pra-pendaftaran.index', request()->except('search')) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-4 space-y-1.5">
                    <label for="status" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Filter Status</label>
                    <select name="status" id="status" onchange="this.form.submit()" 
                            class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 pl-4 py-2">
                        <option value="">Semua Status</option>
                        <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="berkas_tidak_lengkap" {{ request('status') === 'berkas_tidak_lengkap' ? 'selected' : '' }}>Berkas Tidak Lengkap</option>
                        <option value="menunggu_verifikasi_ulang" {{ request('status') === 'menunggu_verifikasi_ulang' ? 'selected' : '' }}>Menunggu Verifikasi Ulang</option>
                        <option value="berkas_lengkap" {{ request('status') === 'berkas_lengkap' ? 'selected' : '' }}>Berkas Lengkap</option>
                        <option value="jadwal_dipilih" {{ request('status') === 'jadwal_dipilih' ? 'selected' : '' }}>Jadwal Dipilih</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>

                <!-- Submit / Clear Buttons -->
                <div class="md:col-span-2 flex gap-2 w-full">
                    <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm h-11 rounded-xl flex-1 text-center transition shadow-md shadow-blue-900/20">
                        Cari
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ route('klien.pra-pendaftaran.index') }}" class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-bold text-sm h-11 px-4 rounded-xl text-center transition flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Success Alert -->
        @if (session('success'))
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 flex gap-3 text-sm text-green-700">
                <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Case List -->
        <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <!-- Desktop Table Layout -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F1F5F9]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Judul Perkara</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#F1F5F9]">
                        @forelse ($praPendaftaranPerkara as $pengajuan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold font-mono text-navy-primary">
                                    PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy-dark">
                                    {{ \Illuminate\Support\Str::limit($pengajuan->judul_perkara, 40) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                    {{ $pengajuan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="inline-flex items-center gap-1 font-semibold text-accent-blue hover:text-navy-dark transition">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="bg-gray-50 p-4 rounded-full w-14 h-14 mx-auto flex items-center justify-center text-gray-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0V9a2 2 0 00-2-2H6a2 2 0 00-2 2v4m16 4h-2m-8 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-navy-dark">Tidak Ada Pengajuan</p>
                                        <p class="text-xs text-gray-400 leading-relaxed">
                                            @if(request('search') || request('status'))
                                                Tidak ditemukan pengajuan dengan kriteria pencarian dan filter Anda saat ini.
                                            @else
                                                Anda belum mengajukan pra-pendaftaran perkara apa pun saat ini.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout -->
            <div class="block md:hidden divide-y divide-[#F1F5F9] bg-white">
                @forelse ($praPendaftaranPerkara as $pengajuan)
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold font-mono text-navy-primary">
                                PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                            </span>
                            <x-status-badge :status="$pengajuan->status_pengajuan" />
                        </div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">{{ $pengajuan->judul_perkara }}</h4>
                            <p class="text-xs text-gray-500 mt-1">Kategori: {{ $pengajuan->kategori?->nama_kategori ?? '-' }}</p>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <span class="text-xs text-gray-400 font-medium">Diajukan: {{ $pengajuan->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline">
                                <span>Detail</span>
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-400">
                        @if(request('search') || request('status'))
                            Tidak ditemukan pengajuan dengan kriteria pencarian dan filter Anda saat ini.
                        @else
                            Anda belum mengajukan pra-pendaftaran perkara apa pun saat ini.
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if ($praPendaftaranPerkara->hasPages())
                <div class="px-6 py-4 bg-[#F8FAFC] border-t border-[#F1F5F9]">
                    {{ $praPendaftaranPerkara->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
