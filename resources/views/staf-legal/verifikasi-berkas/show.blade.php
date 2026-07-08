<x-app-layout title="Detail Pengajuan" :breadcrumbs="[['label' => 'Staf Legal'], ['label' => 'Pengajuan Verifikasi', 'url' => route('staf-legal.verifikasi-berkas.index')], ['label' => 'PP-' . sprintf('%03d', $praPendaftaranPerkara->id_pendaftaran)]]">

    @php
        $isVerifiable = in_array($praPendaftaranPerkara->status_pengajuan, ['menunggu_verifikasi', 'menunggu_verifikasi_ulang']);
    @endphp

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-700 shadow-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-700 shadow-sm border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <!-- Case Title Header Card -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_12px_rgba(15,23,42,0.04)]">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-[10px] text-[13px] font-mono font-bold bg-[#eff6ff] text-[#1e3a8a]">
                            PP-{{ sprintf('%03d', $praPendaftaranPerkara->id_pendaftaran) }}
                        </span>
                        <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                    </div>
                    <h1 class="text-[20px] font-bold text-[#0f172a] leading-tight">{{ $praPendaftaranPerkara->judul_perkara }}</h1>
                    <p class="text-[13px] text-[#64748b]">
                        Periksa data Klien, kronologi, dan dokumen pendukung sebelum melakukan verifikasi berkas.
                    </p>
                </div>
                @if ($isVerifiable)
                    <div class="shrink-0">
                        <a href="{{ route('staf-legal.verifikasi-berkas.verifikasi', $praPendaftaranPerkara) }}" 
                           class="bg-[#1e3a8a] text-white font-semibold text-[13px] tracking-[0.325px] h-[42px] px-[20px] rounded-[14px] flex items-center justify-center gap-2 shadow-md hover:bg-[#1e40af] transition duration-150 cursor-pointer">
                            <svg class="h-[15px] w-[13px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Verifikasi Berkas
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Info Cards side-by-side -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Data Klien Card -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_12px_rgba(15,23,42,0.04)] space-y-4">
                <div class="border-b border-[#f1f5f9] pb-4">
                    <h3 class="font-bold text-[16px] text-[#0f172a]">Data Klien</h3>
                    <p class="text-[13px] text-[#64748b] mt-1">Informasi Klien untuk membantu proses verifikasi.</p>
                </div>

                <div class="divide-y divide-[#f1f5f9]/60">
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[150px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Nama</span>
                        <span class="font-semibold text-[#0f172a]">{{ $praPendaftaranPerkara->klien?->nama ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[150px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Email</span>
                        <span class="font-semibold text-[#0f172a]">{{ $praPendaftaranPerkara->klien?->email ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[150px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Nomor Telepon</span>
                        <span class="font-semibold text-[#0f172a]">{{ $praPendaftaranPerkara->klien?->no_telepon ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[150px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Alamat</span>
                        <span class="text-[#334155]">{{ $praPendaftaranPerkara->klien?->profil?->alamat ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[150px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Nomor Identitas</span>
                        <span class="font-semibold text-[#0f172a]">{{ $praPendaftaranPerkara->klien?->profil?->no_identitas ?? '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- Informasi Pengajuan Card -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_12px_rgba(15,23,42,0.04)] space-y-4">
                <div class="border-b border-[#f1f5f9] pb-4">
                    <h3 class="font-bold text-[16px] text-[#0f172a]">Informasi Pengajuan</h3>
                    <p class="text-[13px] text-[#64748b] mt-1">Detail perkara yang diajukan oleh Klien.</p>
                </div>

                <div class="divide-y divide-[#f1f5f9]/60">
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[120px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Kategori</span>
                        <span class="font-semibold text-[#0f172a]">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[120px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Judul</span>
                        <span class="font-semibold text-[#0f172a]">{{ $praPendaftaranPerkara->judul_perkara }}</span>
                    </div>
                    <div class="py-3 flex items-start gap-4 text-[13px]">
                        <span class="w-[120px] font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px] pt-0.5">Tanggal</span>
                        <span class="font-semibold text-[#0f172a]">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y') ?? '-' }}</span>
                    </div>
                    <div class="py-3 flex flex-col gap-1 text-[13px]">
                        <span class="font-semibold text-[#64748b] uppercase tracking-[0.275px] text-[11px]">Kronologi</span>
                        <p class="text-[#334155] whitespace-pre-line leading-relaxed max-h-[120px] overflow-y-auto pr-2 mt-1">
                            {{ $praPendaftaranPerkara->kronologi }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokumen Pendukung Card -->
        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_12px_rgba(15,23,42,0.04)] space-y-4">
            <div class="border-b border-[#f1f5f9] pb-4">
                <h3 class="font-bold text-[16px] text-[#0f172a]">Dokumen Pendukung</h3>
                <p class="text-[13px] text-[#64748b] mt-1">Buka dokumen melalui link aman untuk memeriksa keabsahan data.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#f8fafc]">
                        <tr>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Nama Dokumen</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Status Dokumen</th>
                            <th class="px-4 py-3 text-right text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#f1f5f9]">
                        @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                            <tr>
                                <td class="px-4 py-3 text-[13px] font-medium text-[#334155]">{{ $dokumen->nama_dokumen }}</td>
                                <td class="px-4 py-3 text-[13px] text-[#64748b]">{{ $dokumen->jenis_dokumen }}</td>
                                <td class="px-4 py-3">
                                    <x-status-badge :status="$dokumen->status_dokumen" />
                                </td>
                                <td class="px-4 py-3 text-right text-[12px] font-semibold">
                                    <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-[#1d4ed8] hover:text-[#1e40af] transition duration-150">
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-[13px] text-[#64748b]">
                                    Belum ada dokumen yang diunggah.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Riwayat Dokumen (jika ada data) -->
        @if ($praPendaftaranPerkara->riwayatDokumen->isNotEmpty())
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-6 shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_12px_rgba(15,23,42,0.04)] space-y-4">
                <div class="border-b border-[#f1f5f9] pb-4">
                    <h3 class="font-bold text-[16px] text-[#0f172a]">Riwayat Dokumen Replaced</h3>
                    <p class="text-[13px] text-[#64748b] mt-1">Dokumen lama yang sudah diganti oleh Klien (read-only).</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#f8fafc]">
                            <tr>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Nama Dokumen</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Jenis</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Tanggal Upload</th>
                                <th class="px-4 py-3 text-right text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#f1f5f9]">
                            @foreach ($praPendaftaranPerkara->riwayatDokumen as $dokumen)
                                <tr>
                                    <td class="px-4 py-3 text-[13px] font-medium text-[#334155]">{{ $dokumen->nama_dokumen }}</td>
                                    <td class="px-4 py-3 text-[13px] text-[#64748b]">{{ $dokumen->jenis_dokumen }}</td>
                                    <td class="px-4 py-3">
                                        <x-status-badge :status="$dokumen->status_dokumen" />
                                    </td>
                                    <td class="px-4 py-3 text-[13px] text-[#64748b]">
                                        {{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-[12px] font-semibold">
                                        <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-[#1d4ed8] hover:text-[#1e40af] transition duration-150">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        
        <!-- Back Navigation link -->
        <div class="flex items-center justify-start mt-4">
            <a href="{{ route('staf-legal.verifikasi-berkas.index') }}" class="inline-flex items-center gap-1 text-[13px] text-[#64748b] hover:text-[#0f172a] transition duration-150">
                <svg class="h-[14px] w-[14px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali ke Daftar Pengajuan
            </a>
        </div>
    </div>
</x-app-layout>
