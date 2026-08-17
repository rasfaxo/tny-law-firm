<x-app-layout title="Data Pengajuan Perkara" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Data Pengajuan']]">

    <div class="space-y-6">
        <!-- Filter & Search Bar -->
        <x-card>
            <form method="GET" action="{{ route('admin.pra-pendaftaran.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search Input -->
                <div class="md:col-span-3 space-y-1.5">
                    <x-input-label for="search" :value="__('Cari Pengajuan')" class="mb-0" />
                    <div class="relative">
                        <x-text-input type="text" name="search" id="search" :value="request('search')" placeholder="Judul, kode, klien..." class="pr-10" />
                        @if(request('search'))
                            <a href="{{ route('admin.pra-pendaftaran.index', request()->except('search')) }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Tanggal Mulai -->
                <div class="md:col-span-2 space-y-1.5">
                    <x-input-label for="tanggal_mulai" :value="__('Tgl Mulai')" class="mb-0" />
                    <x-text-input type="date" name="tanggal_mulai" id="tanggal_mulai" :value="request('tanggal_mulai')" />
                </div>

                <!-- Tanggal Selesai -->
                <div class="md:col-span-2 space-y-1.5">
                    <x-input-label for="tanggal_selesai" :value="__('Tgl Selesai')" class="mb-0" />
                    <x-text-input type="date" name="tanggal_selesai" id="tanggal_selesai" :value="request('tanggal_selesai')" />
                </div>

                <!-- Kategori Filter -->
                <div class="md:col-span-2 space-y-1.5">
                    <x-input-label for="kategori" :value="__('Kategori')" class="mb-0" />
                    <x-select name="kategori" id="kategori">
                        <option value="">Semua</option>
                        @foreach($kategoriList as $kategori)
                            <option value="{{ $kategori->id_kategori }}" {{ request('kategori') == $kategori->id_kategori ? 'selected' : '' }}>
                                {{ $kategori->nama_kategori }}
                            </option>
                        @endforeach
                    </x-select>
                </div>

                <!-- Status Filter -->
                <div class="md:col-span-2 space-y-1.5">
                    <x-input-label for="status" :value="__('Status')" class="mb-0" />
                    <x-select name="status" id="status">
                        <option value="">Semua</option>
                        <option value="menunggu_verifikasi" {{ request('status') === 'menunggu_verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="berkas_tidak_lengkap" {{ request('status') === 'berkas_tidak_lengkap' ? 'selected' : '' }}>Berkas Tidak Lengkap</option>
                        <option value="menunggu_verifikasi_ulang" {{ request('status') === 'menunggu_verifikasi_ulang' ? 'selected' : '' }}>Menunggu Verifikasi Ulang</option>
                        <option value="berkas_lengkap" {{ request('status') === 'berkas_lengkap' ? 'selected' : '' }}>Berkas Lengkap</option>
                        <option value="jadwal_dipilih" {{ request('status') === 'jadwal_dipilih' ? 'selected' : '' }}>Jadwal Dipilih</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </x-select>
                </div>

                <!-- Submit Buttons -->
                <div class="md:col-span-1 flex items-center justify-end h-11">
                    <button type="submit" class="bg-navy-primary hover:bg-navy-dark text-white font-bold text-sm h-full w-full rounded-xl flex items-center justify-center transition shadow-md shadow-blue-900/20" title="Terapkan Filter">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
            @if(request()->anyFilled(['search', 'tanggal_mulai', 'tanggal_selesai', 'kategori', 'status']))
                <div class="mt-4 flex justify-end">
                    <a href="{{ route('admin.pra-pendaftaran.index') }}" class="text-xs font-semibold text-accent-blue hover:underline">
                        Reset Filter
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Success Alert -->
        @if (session('success'))
            <x-alert-banner type="success">
                {{ session('success') }}
            </x-alert-banner>
        @endif

        <!-- Case List -->
        <x-card class="p-0 overflow-hidden">
            <!-- Desktop Table Layout -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-[#F1F5F9]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Klien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Judul Perkara</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tgl Pengajuan</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#F1F5F9]">
                        @forelse ($pengajuan as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold font-mono text-navy-primary">
                                    PP-{{ str_pad($item->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-navy-dark">
                                    {{ $item->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-navy-dark">
                                    {{ \Illuminate\Support\Str::limit($item->judul_perkara, 40) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->kategori?->nama_kategori ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$item->status_pengajuan" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400">
                                    {{ $item->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('admin.pra-pendaftaran.show', $item) }}" class="inline-flex items-center gap-1 font-semibold text-accent-blue hover:text-navy-dark transition">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <x-empty-state title="Tidak Ada Data" message="Tidak ditemukan data pra-pendaftaran perkara yang cocok dengan kriteria filter Anda." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout -->
            <div class="block md:hidden divide-y divide-[#F1F5F9] bg-white">
                @forelse ($pengajuan as $item)
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold font-mono text-navy-primary">
                                PP-{{ str_pad($item->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                            </span>
                            <x-status-badge :status="$item->status_pengajuan" />
                        </div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">{{ $item->judul_perkara }}</h4>
                            <p class="text-xs text-gray-500 mt-1"><span class="font-medium">Klien:</span> {{ $item->klien?->nama ?? '-' }}</p>
                            <p class="text-xs text-gray-500 mt-0.5"><span class="font-medium">Kategori:</span> {{ $item->kategori?->nama_kategori ?? '-' }}</p>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <span class="text-xs text-gray-400 font-medium">{{ $item->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                            <a href="{{ route('admin.pra-pendaftaran.show', $item) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline">
                                <span>Detail</span>
                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-400">
                        <x-empty-state title="Tidak Ada Data" message="Tidak ditemukan data pra-pendaftaran perkara yang cocok dengan kriteria filter Anda." />
                    </div>
                @endforelse
            </div>

            <!-- Pagination Links -->
            @if ($pengajuan->hasPages())
                <div class="px-6 py-4 bg-[#F8FAFC] border-t border-[#F1F5F9]">
                    {{ $pengajuan->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
