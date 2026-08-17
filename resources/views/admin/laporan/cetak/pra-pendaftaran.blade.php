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
            <p class="text-xs text-slate-600 font-medium">
                Rekapitulasi Berkas Permohonan Masuk & Status Verifikasi • {{ config('firm.name', 'TNY & PARTNERS') }}
            </p>
        </div>

        <!-- Filter & Print Metadata Info Box -->
        <div class="bg-slate-50 border border-slate-300 rounded-lg p-3.5 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Periode</span>
                <span class="font-bold text-slate-900">
                    @if (!empty($filters['tanggal_mulai']) || !empty($filters['tanggal_selesai']))
                        {{ !empty($filters['tanggal_mulai']) ? \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') : 'Awal' }} s/d {{ !empty($filters['tanggal_selesai']) ? \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') : 'Sekarang' }}
                    @else
                        Semua Data
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Filter Kategori / Status</span>
                <span class="font-bold text-slate-900">
                    {{ $selectedKategori->nama_kategori ?? 'Semua Kategori' }}
                    @if (!empty($filters['status_pengajuan']))
                        • {{ $statusLabels[$filters['status_pengajuan']] ?? $filters['status_pengajuan'] }}
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Data Ditemukan</span>
                <span class="font-bold text-slate-900">{{ $laporan->count() }} Data Perkara</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Waktu Pencetakan</span>
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
                        <th class="border border-slate-400 px-3 py-2 text-center w-32">Status</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-28">Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($laporan as $pengajuan)
                        <tr>
                            <td class="border border-slate-400 px-2 py-2 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="border border-slate-400 px-3 py-2 font-mono font-bold text-blue-900 whitespace-nowrap">
                                PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">
                                {{ $pengajuan->klien?->nama ?? 'Tidak diketahui' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-800">
                                {{ $pengajuan->kategori?->nama_kategori ?? 'Umum' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-700">
                                {{ $pengajuan->judul_perkara }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide
                                    @if ($pengajuan->status_pengajuan === 'berkas_lengkap' || $pengajuan->status_pengajuan === 'selesai')
                                        bg-emerald-100 text-emerald-900 border border-emerald-400
                                    @elseif ($pengajuan->status_pengajuan === 'berkas_tidak_lengkap')
                                        bg-rose-100 text-rose-900 border border-rose-400
                                    @elseif ($pengajuan->status_pengajuan === 'jadwal_dipilih')
                                        bg-blue-100 text-blue-900 border border-blue-400
                                    @else
                                        bg-amber-100 text-amber-900 border border-amber-400
                                    @endif">
                                    {{ $statusLabels[$pengajuan->status_pengajuan] ?? $pengajuan->status_pengajuan }}
                                </span>
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-600 font-mono text-[11px] whitespace-nowrap">
                                {{ $pengajuan->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Tidak ada data pra-pendaftaran perkara yang sesuai dengan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
