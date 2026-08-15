<x-print-layout title="Laporan Pra-Pendaftaran Perkara">
    @php
        $statusLabels = [
            'menunggu_verifikasi' => 'Menunggu Verifikasi',
            'berkas_tidak_lengkap' => 'Berkas Tidak Lengkap',
            'menunggu_verifikasi_ulang' => 'Menunggu Verifikasi Ulang',
            'berkas_lengkap' => 'Berkas Lengkap',
            'jadwal_dipilih' => 'Jadwal Dipilih',
            'selesai' => 'Selesai',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Title Block -->
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold uppercase tracking-wide text-slate-900 underline font-serif">
                Laporan Pra-Pendaftaran Perkara
            </h2>
            <p class="text-xs text-slate-600">
                Rekapitulasi Berkas Permohonan Masuk & Status Verifikasi
            </p>
        </div>

        <!-- Filter & Print Metadata Info Box -->
        <div class="bg-slate-50 border border-slate-300 rounded-lg p-3.5 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Periode</span>
                <span class="font-bold text-slate-900">
                    @if (($filters['tanggal_mulai'] ?? null) || ($filters['tanggal_selesai'] ?? null))
                        {{ $filters['tanggal_mulai'] ? \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') : 'Awal' }} s/d {{ $filters['tanggal_selesai'] ? \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') : 'Sekarang' }}
                    @else
                        Semua Data
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Filter Kategori / Status</span>
                <span class="font-bold text-slate-900">
                    {{ $selectedKategori->nama_kategori ?? 'Semua Kategori' }}
                    @if ($filters['status_pengajuan'] ?? null)
                        • {{ $statusLabels[$filters['status_pengajuan']] ?? $filters['status_pengajuan'] }}
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Total Data Ditemukan</span>
                <span class="font-bold text-slate-900">{{ $laporan->count() }} Data Perkara</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Waktu Pencetakan</span>
                <span class="font-medium text-slate-800">{{ \Carbon\Carbon::now()->translatedFormat('d M Y, H:i') }} WIB</span>
            </div>
        </div>

        <!-- Data Table -->
        <div>
            <table class="w-full border-collapse border border-slate-400 text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-800 font-semibold">
                        <th class="border border-slate-400 px-2 py-2 text-center w-8">No</th>
                        <th class="border border-slate-400 px-3 py-2 text-left w-20">Kode</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Nama Klien</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Kategori Perkara</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Judul Perkara</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-28">Status</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-24">Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($laporan as $pengajuan)
                        <tr>
                            <td class="border border-slate-400 px-2 py-2 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="border border-slate-400 px-3 py-2 font-mono font-bold text-blue-900">
                                PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">
                                {{ $pengajuan->klien?->nama ?? '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-800">
                                {{ $pengajuan->kategori?->nama_kategori ?? '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-700">
                                {{ $pengajuan->judul_perkara }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold">
                                <span class="px-1.5 py-0.5 rounded text-[10.5px] 
                                    @if ($pengajuan->status_pengajuan === 'berkas_lengkap' || $pengajuan->status_pengajuan === 'selesai')
                                        bg-emerald-100 text-emerald-800 border border-emerald-300
                                    @elseif ($pengajuan->status_pengajuan === 'berkas_tidak_lengkap')
                                        bg-rose-100 text-rose-800 border border-rose-300
                                    @elseif ($pengajuan->status_pengajuan === 'jadwal_dipilih')
                                        bg-blue-100 text-blue-800 border border-blue-300
                                    @else
                                        bg-amber-100 text-amber-800 border border-amber-300
                                    @endif">
                                    {{ $statusLabels[$pengajuan->status_pengajuan] ?? $pengajuan->status_pengajuan }}
                                </span>
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-600 font-mono text-[11px]">
                                {{ $pengajuan->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Tidak ada data pra-pendaftaran perkara yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
