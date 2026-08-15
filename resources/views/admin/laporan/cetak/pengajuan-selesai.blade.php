<x-print-layout title="Laporan Pengajuan Selesai">
    <div class="space-y-6">
        <!-- Title Block -->
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold uppercase tracking-wide text-slate-900 underline font-serif">
                Laporan Pengajuan Perkara Selesai (Tuntas)
            </h2>
            <p class="text-xs text-slate-600">
                Rekapitulasi Perkara yang Telah Menyelesaikan Seluruh Tahapan Pra-Pendaftaran & Konsultasi
            </p>
        </div>

        <!-- Filter & Print Metadata Info Box -->
        <div class="bg-slate-50 border border-slate-300 rounded-lg p-3.5 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Periode Selesai</span>
                <span class="font-bold text-slate-900">
                    @if (($filters['tanggal_mulai'] ?? null) || ($filters['tanggal_selesai'] ?? null))
                        {{ $filters['tanggal_mulai'] ? \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') : 'Awal' }} s/d {{ $filters['tanggal_selesai'] ? \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') : 'Sekarang' }}
                    @else
                        Semua Data
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Kategori Perkara</span>
                <span class="font-bold text-slate-900">
                    {{ $selectedKategori->nama_kategori ?? 'Semua Kategori' }}
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Total Perkara Tuntas</span>
                <span class="font-bold text-emerald-800">{{ $laporan->count() }} Perkara</span>
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
                        <th class="border border-slate-400 px-3 py-2 text-center w-24">Tgl Diajukan</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-24">Tgl Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($laporan as $pengajuan)
                        @php
                            $tglSelesai = $pengajuan->riwayatStatus->first()?->created_at ?? $pengajuan->updated_at;
                        @endphp
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
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-600 font-mono text-[11px]">
                                {{ $pengajuan->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold text-emerald-800 font-mono text-[11px]">
                                {{ $tglSelesai ? $tglSelesai->format('d/m/Y H:i') : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Tidak ada data pengajuan perkara selesai yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
