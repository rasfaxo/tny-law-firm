<x-app-layout title="Detail Pra-Pendaftaran" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Laporan', 'url' => route('admin.laporan.index')], ['label' => 'Detail Pra-Pendaftaran']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <x-secondary-button href="{{ route('admin.laporan.pra-pendaftaran') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>
        </div>
        <!-- Monitoring Info Banner (Figma Node 82:2210) -->
        <x-alert-banner type="info" title="Halaman Monitoring Admin">
            Halaman ini digunakan untuk memantau detail pra-pendaftaran perkara klien. Pengungahan berkas dan verifikasi berkas merupakan wewenang dari Klien dan Staf Legal.
        </x-alert-banner>

        <!-- Case Title & Status -->
        <x-card class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="space-y-1">
                <span class="inline-flex bg-blue-50 text-[#1e3a8a] text-xs font-bold font-mono px-3 py-1 rounded-lg">
                    PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                </span>
                <h3 class="font-extrabold text-xl text-navy-dark">{{ $praPendaftaranPerkara->judul_perkara }}</h3>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400 font-medium">Status Pengajuan:</span>
                <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
            </div>
        </x-card>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Card: Data Klien -->
            <x-card class="space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Data Klien</h3>
                    <p class="text-xs text-gray-400 mt-1">Informasi akun dan profil klien pemilik pengajuan.</p>
                </div>

                @php
                    $profil = $praPendaftaranPerkara->klien?->profilKlien;
                @endphp

                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0 flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $praPendaftaranPerkara->klien?->nama ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $praPendaftaranPerkara->klien?->email ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Nomor Telepon</span>
                        <span class="text-sm font-semibold text-navy-dark font-mono">{{ $praPendaftaranPerkara->klien?->no_telepon ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">NIK</span>
                        <span class="text-sm font-semibold text-navy-dark font-mono">{{ $profil?->nik ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Pekerjaan</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $profil?->pekerjaan ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between items-start gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Alamat Lengkap</span>
                        <span class="text-sm font-semibold text-navy-dark leading-relaxed text-left max-w-xs">{{ $profil?->alamat ?? '-' }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Right Card: Detail Perkara -->
            <x-card class="space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Detail Perkara</h3>
                    <p class="text-xs text-gray-400 mt-1">Klasifikasi dan deskripsi permasalahan hukum.</p>
                </div>

                <div class="space-y-4 divide-y divide-[#E2E8F0]">
                    <div class="pt-0 flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Kategori Perkara</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                        <span class="text-sm font-semibold text-navy-dark">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</span>
                    </div>

                    <div class="flex flex-col items-start gap-2 py-3">
                        <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Kronologi Perkara</span>
                        <div class="text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl w-full text-left whitespace-pre-line mt-2">
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
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Tipe / Format</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Dokumen</th>
                            <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi Berkas</th>
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
                                            default => 'amber',
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
                                    <a href="{{ route('dokumen.view', $dokumen) }}" target="_blank" 
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1D4ED8] hover:underline transition">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        <span>Buka Dokumen</span>
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
