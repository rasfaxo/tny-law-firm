<x-app-layout title="Daftar Pengajuan Perkara" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Daftar Pengajuan']]">

    <div class="space-y-6">
        <div class="flex justify-end">
            <!-- CTA Buat Pengajuan -->
            <x-primary-button href="{{ route('klien.pra-pendaftaran.create') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Buat Pengajuan</span>
            </x-primary-button>
        </div>
        <!-- Filter & Search Bar -->
        <x-card>
            <form method="GET" action="{{ route('klien.pra-pendaftaran.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search Input -->
                <div class="md:col-span-6 space-y-1.5">
                    <x-input-label for="search" :value="__('Cari Pengajuan')" />
                    <div class="relative">
                        <x-text-input type="text" name="search" id="search" :value="request('search')" placeholder="Judul atau kode pengajuan..." class="w-full pl-4 pr-10" />
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
                    <x-input-label for="status" :value="__('Filter Status')" />
                    <x-select name="status" id="status" onchange="this.form.submit()" class="mt-1">
                        <option value="">Semua Status</option>
                        <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="berkas_tidak_lengkap" {{ request('status') === 'berkas_tidak_lengkap' ? 'selected' : '' }}>Berkas Tidak Lengkap</option>
                        <option value="menunggu_verifikasi_ulang" {{ request('status') === 'menunggu_verifikasi_ulang' ? 'selected' : '' }}>Menunggu Verifikasi Ulang</option>
                        <option value="berkas_lengkap" {{ request('status') === 'berkas_lengkap' ? 'selected' : '' }}>Berkas Lengkap</option>
                        <option value="jadwal_dipilih" {{ request('status') === 'jadwal_dipilih' ? 'selected' : '' }}>Jadwal Dipilih</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </x-select>
                </div>

                <!-- Submit / Clear Buttons -->
                <div class="md:col-span-2 flex gap-2 w-full">
                    <x-primary-button type="submit" class="flex-1 justify-center h-11 px-4">
                        Cari
                    </x-primary-button>
                    @if(request('search') || request('status'))
                        <x-secondary-button href="{{ route('klien.pra-pendaftaran.index') }}" tag="a" class="h-11 px-4 justify-center">
                            Reset
                        </x-secondary-button>
                    @endif
                </div>
            </form>
        </x-card>

        <!-- Success Alert -->
        @if (session('success'))
            <x-alert-banner type="success">
                {{ session('success') }}
            </x-alert-banner>
        @endif

        <!-- Case List -->
        <x-card class="p-0 overflow-hidden sm:p-0">
            <!-- Desktop Table Layout -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
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
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($praPendaftaranPerkara as $pengajuan)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold font-mono text-navy-primary">
                                    PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy-dark">
                                    {{ \Illuminate\Support\Str::limit($pengajuan->judul_perkara, 40) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">
                                    {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$pengajuan->status_pengajuan" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                    {{ $pengajuan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                    <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-navy-dark hover:text-accent-blue hover:underline transition">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        title="Tidak Ada Pengajuan" 
                                        :message="request('search') || request('status') ? 'Tidak ditemukan pengajuan dengan kriteria pencarian dan filter Anda saat ini.' : 'Anda belum mengajukan pra-pendaftaran perkara apa pun saat ini.'" 
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout -->
            <div class="block md:hidden divide-y divide-[#E2E8F0] bg-white">
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
                            <span class="text-xs text-gray-400 font-medium">{{ $pengajuan->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline">
                                <span>Detail</span>
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <x-empty-state 
                            title="Tidak Ada Pengajuan" 
                            :message="request('search') || request('status') ? 'Tidak ditemukan pengajuan dengan kriteria pencarian dan filter Anda saat ini.' : 'Anda belum mengajukan pra-pendaftaran perkara apa pun saat ini.'" 
                        />
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if ($praPendaftaranPerkara->hasPages())
                <div class="px-6 py-4 bg-[#F8FAFC] border-t border-[#E2E8F0]">
                    {{ $praPendaftaranPerkara->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
