<x-print-layout title="Laporan Pengajuan Selesai">
    <div class="space-y-6">
        <!-- Title Block -->
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold uppercase tracking-wide text-slate-900 underline font-serif">
                Laporan Pengajuan Perkara Selesai (Tuntas)
            </h2>
            <p class="text-xs text-slate-600 font-medium">
                Rekapitulasi Perkara yang Telah Menyelesaikan Seluruh Tahapan Pra-Pendaftaran & Konsultasi • {{ config('firm.name', 'TNY & PARTNERS') }}
            </p>
        </div>

        <!-- Filter & Print Metadata Info Box -->
        <div class="bg-slate-50 border border-slate-300 rounded-lg p-3.5 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Periode Selesai</span>
                <span class="font-bold text-slate-900">
                    @if (!empty($filters['tanggal_mulai']) || !empty($filters['tanggal_selesai']))
                        {{ !empty($filters['tanggal_mulai']) ? \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') : 'Awal' }} s/d {{ !empty($filters['tanggal_selesai']) ? \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') : 'Sekarang' }}
                    @else
                        Semua Data
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Kategori Perkara</span>
                <span class="font-bold text-slate-900">
                    {{ $selectedKategori->nama_kategori ?? 'Semua Kategori' }}
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Perkara Tuntas</span>
                <span class="font-bold text-emerald-900">{{ $laporan->count() }} Perkara</span>
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
                        <th class="border border-slate-400 px-3 py-2 text-center w-28">Tgl Diajukan</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-28">Tgl Selesai</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-24">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($laporan as $pengajuan)
                        @php
                            $tglSelesai = $pengajuan->riwayatStatus->where('status', 'selesai')->first()?->created_at ?? $pengajuan->updated_at;
                        @endphp
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
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-600 font-mono text-[11px] whitespace-nowrap">
                                {{ $pengajuan->tanggal_pengajuan?->format('d/m/Y') ?? '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center text-emerald-950 font-mono font-semibold text-[11px] whitespace-nowrap">
                                {{ $tglSelesai?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide bg-emerald-100 text-emerald-900 border border-emerald-400">
                                    Selesai
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Tidak ada data perkara selesai yang sesuai dengan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
