<x-app-layout title="Detail Pra-Pendaftaran" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Laporan', 'url' => route('admin.laporan.index')], ['label' => 'Detail Pra-Pendaftaran']]">

    <div class="space-y-6">
        <!-- Header Card -->
        <x-card class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex bg-blue-50 text-[#1e3a8a] text-xs font-bold font-mono px-3 py-1.5 rounded-lg">
                        PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                    </span>
                    <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                    <span class="inline-flex bg-indigo-50 text-indigo-600 text-xs font-bold px-3 py-1.5 rounded-lg">
                        Monitoring
                    </span>
                </div>
                <h3 class="font-extrabold text-2xl text-navy-dark">{{ $praPendaftaranPerkara->judul_perkara }}</h3>
            </div>
            <div class="shrink-0">
                <x-secondary-button href="{{ route('admin.laporan.pra-pendaftaran') }}" tag="a" class="gap-2 h-10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ __('Kembali') }}</span>
                </x-secondary-button>
            </div>
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Card: Data Klien -->
            <x-card class="space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Data Klien</h3>
                    <p class="text-xs text-gray-400 mt-1">Informasi Klien pemilik pengajuan.</p>
                </div>

                @php
                    $profil = $praPendaftaranPerkara->klien?->profilKlien;
                @endphp

                <div class="space-y-0 divide-y divide-[#E2E8F0]">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4 pt-0">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Nama</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->klien?->nama ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Email</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->klien?->email ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Telepon</div>
                        <div class="text-sm font-semibold text-navy-dark font-mono md:col-span-2">{{ $praPendaftaranPerkara->klien?->no_telepon ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Alamat</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2 leading-relaxed">{{ $profil?->alamat ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">NIK</div>
                        <div class="text-sm font-semibold text-navy-dark font-mono md:col-span-2">{{ $profil?->nik ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4 pb-0">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Pekerjaan</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $profil?->pekerjaan ?? '-' }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Right Card: Informasi Pengajuan -->
            <x-card class="space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Informasi Pengajuan</h3>
                    <p class="text-xs text-gray-400 mt-1">Data pokok pra-pendaftaran perkara.</p>
                </div>

                <div class="space-y-0 divide-y divide-[#E2E8F0]">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4 pt-0">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Kategori</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Judul</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->judul_perkara }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Tanggal</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y') ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider flex items-center">Status</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">
                            {{ Str::title(str_replace('_', ' ', $praPendaftaranPerkara->status_pengajuan)) }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4 pb-0">
                        <div class="text-xxs font-bold text-gray-400 uppercase tracking-wider mt-1">Kronologi</div>
                        <div class="text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl w-full text-left whitespace-pre-line md:col-span-2">
                            {{ $praPendaftaranPerkara->kronologi }}
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Documents Card -->
        <x-card class="space-y-6">
            <div>
                <h3 class="font-bold text-navy-dark text-lg">Dokumen Pendukung</h3>
                <p class="text-xs text-gray-400 mt-1">Berkas perkara yang diunggah oleh klien.</p>
            </div>

            <div class="overflow-x-auto border border-[#E2E8F0] rounded-xl">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($praPendaftaranPerkara->dokumenPerkara as $dokumen)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-navy-dark text-sm">{{ $dokumen->nama_dokumen }}</div>
                                    <div class="text-xxs text-gray-400 mt-0.5 font-mono">Diupload: {{ $dokumen->created_at?->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ strtoupper(pathinfo($dokumen->file_path, PATHINFO_EXTENSION)) ?: 'PDF' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $docStatusColor = match ($dokumen->status_dokumen) {
                                            'valid' => 'emerald',
                                            'perlu_perbaikan' => 'rose',
                                            'diganti' => 'gray',
                                            default => 'blue',
                                        };
                                        $docStatusLabel = match ($dokumen->status_dokumen) {
                                            'valid' => 'Valid',
                                            'perlu_perbaikan' => 'Perlu Perbaikan',
                                            'diganti' => 'Diganti',
                                            default => 'Terkirim',
                                        };
                                    @endphp
                                    <span class="inline-flex rounded-full bg-{{ $docStatusColor }}-100 border border-{{ $docStatusColor }}-200 px-2.5 py-0.5 text-xxs font-extrabold uppercase tracking-wider text-{{ $docStatusColor }}-800">
                                        {{ $docStatusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.dokumen.show', $dokumen) }}" target="_blank" 
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1D4ED8] hover:underline transition">
                                        <span>Lihat Dokumen</span>
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <x-empty-state title="Belum Ada Dokumen" message="Klien belum mengunggah dokumen pendukung untuk perkara ini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
            </div>
        </x-card>
    </div>
</x-app-layout>
