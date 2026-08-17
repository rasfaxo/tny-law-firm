<x-app-layout title="Detail Pengajuan" :breadcrumbs="[['label' => 'Staf Legal'], ['label' => 'Pengajuan Verifikasi', 'url' => route('staf-legal.verifikasi-berkas.index')], ['label' => 'PP-' . sprintf('%03d', $praPendaftaranPerkara->id_pendaftaran)]]">

    @php
        $isVerifiable = in_array($praPendaftaranPerkara->status_pengajuan, ['menunggu_verifikasi', 'menunggu_verifikasi_ulang']);
    @endphp

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

        <!-- Case Title Header Card -->
        <x-card>
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-mono font-bold bg-blue-50 text-blue-900">
                            PP-{{ sprintf('%03d', $praPendaftaranPerkara->id_pendaftaran) }}
                        </span>
                        <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                    </div>
                    <h1 class="text-2xl font-extrabold text-navy-dark leading-tight">{{ $praPendaftaranPerkara->judul_perkara }}</h1>
                    <p class="text-sm text-gray-500">
                        Periksa data Klien, kronologi, dan dokumen pendukung sebelum melakukan verifikasi berkas.
                    </p>
                </div>
                @if ($isVerifiable)
                    <div class="shrink-0">
                        <x-primary-button href="{{ route('staf-legal.verifikasi-berkas.verifikasi', $praPendaftaranPerkara) }}" tag="a">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verifikasi Berkas
                        </x-primary-button>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Info Cards side-by-side -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Data Klien Card -->
            <x-card class="space-y-4">
                <div class="border-b border-[#F1F5F9] pb-4">
                    <h3 class="font-bold text-lg text-navy-dark">Data Klien</h3>
                    <p class="text-xs text-gray-500 mt-1">Informasi Klien untuk membantu proses verifikasi.</p>
                </div>

                <div class="divide-y divide-[#F1F5F9]">
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-36 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Nama</span>
                        <span class="font-semibold text-navy-dark">{{ $praPendaftaranPerkara->klien?->nama ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-36 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Email</span>
                        <span class="font-semibold text-navy-dark">{{ $praPendaftaranPerkara->klien?->email ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-36 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Nomor Telepon</span>
                        <span class="font-semibold text-navy-dark">{{ $praPendaftaranPerkara->klien?->no_telepon ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-36 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Alamat</span>
                        <span class="text-gray-700">{{ $praPendaftaranPerkara->klien?->profil?->alamat ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-36 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Nomor Identitas</span>
                        <span class="font-semibold text-navy-dark">{{ $praPendaftaranPerkara->klien?->profil?->no_identitas ?? '-' }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Informasi Pengajuan Card -->
            <x-card class="space-y-4">
                <div class="border-b border-[#F1F5F9] pb-4">
                    <h3 class="font-bold text-lg text-navy-dark">Informasi Pengajuan</h3>
                    <p class="text-xs text-gray-500 mt-1">Detail perkara yang diajukan oleh Klien.</p>
                </div>

                <div class="divide-y divide-[#F1F5F9]">
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-32 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Kategori</span>
                        <span class="font-semibold text-navy-dark">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-32 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Judul</span>
                        <span class="font-semibold text-navy-dark">{{ $praPendaftaranPerkara->judul_perkara }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-sm">
                        <span class="w-32 font-bold text-gray-400 uppercase tracking-wider text-xs pt-0.5">Tanggal</span>
                        <span class="font-semibold text-navy-dark">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex flex-col gap-1 text-sm">
                        <span class="font-bold text-gray-400 uppercase tracking-wider text-xs">Kronologi</span>
                        <p class="text-gray-700 whitespace-pre-line leading-relaxed max-h-[120px] overflow-y-auto pr-2 mt-1">
                            {{ $praPendaftaranPerkara->kronologi }}
                        </p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Dokumen Pendukung Card -->
        <x-card class="space-y-4">
            <div class="border-b border-[#F1F5F9] pb-4">
                <h3 class="font-bold text-lg text-navy-dark">Dokumen Pendukung</h3>
                <p class="text-xs text-gray-500 mt-1">Buka dokumen melalui link aman untuk memeriksa keabsahan data.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Nama Dokumen</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Jenis</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Status Dokumen</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 tracking-wider uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#F1F5F9]">
                        @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                            <tr>
                                <td class="px-6 py-4 text-sm font-semibold text-navy-dark">{{ $dokumen->nama_dokumen }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $dokumen->jenis_dokumen }}</td>
                                <td class="px-6 py-4">
                                    <x-status-badge :status="$dokumen->status_dokumen" />
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold">
                                    <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-accent-blue hover:text-blue-800 transition duration-150">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                    <x-empty-state title="Belum ada dokumen" message="Belum ada dokumen yang diunggah." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Riwayat Dokumen (jika ada data) -->
        @if ($praPendaftaranPerkara->riwayatDokumen->isNotEmpty())
            <x-card class="space-y-4">
                <div class="border-b border-[#F1F5F9] pb-4">
                    <h3 class="font-bold text-lg text-navy-dark">Riwayat Dokumen Replaced</h3>
                    <p class="text-xs text-gray-500 mt-1">Dokumen lama yang sudah diganti oleh Klien (read-only).</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Nama Dokumen</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Jenis</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Tanggal Upload</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 tracking-wider uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#F1F5F9]">
                            @foreach ($praPendaftaranPerkara->riwayatDokumen as $dokumen)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-semibold text-navy-dark">{{ $dokumen->nama_dokumen }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $dokumen->jenis_dokumen }}</td>
                                    <td class="px-6 py-4">
                                        <x-status-badge :status="$dokumen->status_dokumen" />
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold">
                                        <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-accent-blue hover:text-blue-800 transition duration-150">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif
        
        <!-- Back Navigation link -->
        <div class="pt-4 pb-12">
            <x-secondary-button href="{{ route('staf-legal.verifikasi-berkas.index') }}" tag="a">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar Pengajuan
            </x-secondary-button>
        </div>
    </div>
</x-app-layout>
