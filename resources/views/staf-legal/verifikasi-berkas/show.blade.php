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
        <x-card class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex bg-blue-50 text-accent-blue text-xs font-bold font-mono px-3 py-1.5 rounded-lg">
                        PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                    </span>
                    <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                </div>
                <h3 class="font-extrabold text-2xl text-navy-dark">{{ $praPendaftaranPerkara->judul_perkara }}</h3>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <x-secondary-button href="{{ route('staf-legal.verifikasi-berkas.index') }}" tag="a" class="gap-2 h-10">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ __('Kembali') }}</span>
                </x-secondary-button>
                @if ($isVerifiable)
                    <x-primary-button href="{{ route('staf-legal.verifikasi-berkas.verifikasi', $praPendaftaranPerkara) }}" tag="a" class="gap-2 h-10">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>Verifikasi Berkas</span>
                    </x-primary-button>
                @endif
            </div>
        </x-card>

        <!-- Info Cards side-by-side -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Data Klien Card -->
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
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Nama</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->klien?->nama ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Email</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->klien?->email ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Telepon</div>
                        <div class="text-sm font-semibold text-navy-dark font-mono md:col-span-2">{{ $praPendaftaranPerkara->klien?->no_telepon ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Alamat</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2 leading-relaxed">{{ $profil?->alamat ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Nomor Identitas</div>
                        <div class="text-sm font-semibold text-navy-dark font-mono md:col-span-2">{{ $profil?->no_identitas ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4 pb-0">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Pekerjaan</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $profil?->pekerjaan ?? '-' }}</div>
                    </div>
                </div>
            </x-card>

            <!-- Informasi Pengajuan Card -->
            <x-card class="space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Informasi Pengajuan</h3>
                    <p class="text-xs text-gray-400 mt-1">Data pokok pra-pendaftaran perkara.</p>
                </div>

                <div class="space-y-0 divide-y divide-[#E2E8F0]">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4 pt-0">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Kategori</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Judul</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->judul_perkara }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Tanggal</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y') ?? '-' }}</div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center">Status</div>
                        <div class="text-sm font-semibold text-navy-dark md:col-span-2">
                            <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 py-4 pb-0">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">Kronologi</div>
                        <div class="text-sm text-gray-600 leading-relaxed bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl w-full text-left whitespace-pre-line md:col-span-2">
                            {{ $praPendaftaranPerkara->kronologi }}
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Dokumen Pendukung Card -->
        <x-card class="space-y-6">
            <div>
                <h3 class="font-bold text-navy-dark text-lg">Dokumen Pendukung</h3>
                <p class="text-xs text-gray-400 mt-1">Buka dokumen melalui link aman untuk memeriksa keabsahan berkas.</p>
            </div>

            <div class="overflow-x-auto border border-[#E2E8F0] rounded-xl">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status Dokumen</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-navy-dark text-sm">{{ $dokumen->nama_dokumen }}</div>
                                    <div class="text-xs text-gray-400 mt-0.5 font-mono">Diupload: {{ $dokumen->created_at?->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ $dokumen->jenis_dokumen }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$dokumen->status_dokumen" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" target="_blank" 
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-accent-blue hover:underline transition">
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
                                    <x-empty-state title="Belum ada dokumen" message="Belum ada dokumen yang diunggah untuk perkara ini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        <!-- Riwayat Dokumen (jika ada data) -->
        @if ($praPendaftaranPerkara->riwayatDokumen->isNotEmpty())
            <x-card class="space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-lg">Riwayat Dokumen Replaced</h3>
                    <p class="text-xs text-gray-400 mt-1">Dokumen lama yang sudah diganti oleh Klien (read-only).</p>
                </div>

                <div class="overflow-x-auto border border-[#E2E8F0] rounded-xl">
                    <table class="min-w-full divide-y divide-[#E2E8F0]">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Upload</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#E2E8F0]">
                            @foreach ($praPendaftaranPerkara->riwayatDokumen as $dokumen)
                                <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                    <td class="px-6 py-4 text-sm font-semibold text-navy-dark">{{ $dokumen->nama_dokumen }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">{{ $dokumen->jenis_dokumen }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$dokumen->status_dokumen" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                        {{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" target="_blank" 
                                            class="inline-flex items-center gap-1.5 text-xs font-bold text-accent-blue hover:underline transition">
                                            <span>Lihat Dokumen</span>
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif
    </div>
</x-app-layout>
