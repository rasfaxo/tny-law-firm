<x-print-layout title="Laporan Verifikasi Berkas">
    @php
        $statusLabels = [
            'berkas_lengkap' => 'Berkas Lengkap',
            'berkas_tidak_lengkap' => 'Berkas Tidak Lengkap',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Title Block -->
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold uppercase tracking-wide text-slate-900 underline font-serif">
                Laporan Hasil Verifikasi Berkas
            </h2>
            <p class="text-xs text-slate-600 font-medium">
                Rekapitulasi Riwayat Pemeriksaan Berkas Perkara oleh Tim Staf Legal • {{ config('firm.name', 'TNY & PARTNERS') }}
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
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Staf Legal / Status</span>
                <span class="font-bold text-slate-900">
                    {{ $selectedStafLegal->nama ?? 'Semua Staf Legal' }}
                    @if (!empty($filters['status_verifikasi']))
                        • {{ $statusLabels[$filters['status_verifikasi']] ?? $filters['status_verifikasi'] }}
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Data Ditemukan</span>
                <span class="font-bold text-slate-900">{{ $laporan->count() }} Data Verifikasi</span>
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
                        <th class="border border-slate-400 px-3 py-2 text-left">Staf Legal Verifikator</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-32">Hasil Verifikasi</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-28">Tgl Verifikasi</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Catatan Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($laporan as $verifikasi)
                        <tr>
                            <td class="border border-slate-400 px-2 py-2 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="border border-slate-400 px-3 py-2 font-mono font-bold text-blue-900 whitespace-nowrap">
                                VR-{{ str_pad($verifikasi->id_verifikasi, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">
                                {{ $verifikasi->praPendaftaranPerkara?->klien?->nama ?? 'Tidak diketahui' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-800">
                                {{ $verifikasi->praPendaftaranPerkara?->kategori?->nama_kategori ?? 'Umum' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-900 font-medium">
                                {{ $verifikasi->stafLegal?->nama ?? 'Tidak diketahui' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide
                                    @if ($verifikasi->status_verifikasi === 'berkas_lengkap')
                                        bg-emerald-100 text-emerald-900 border border-emerald-400
                                    @else
                                        bg-rose-100 text-rose-900 border border-rose-400
                                    @endif">
                                    {{ $statusLabels[$verifikasi->status_verifikasi] ?? $verifikasi->status_verifikasi }}
                                </span>
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-600 font-mono text-[11px] whitespace-nowrap">
                                {{ $verifikasi->tanggal_verifikasi?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-700 text-[11px]">
                                {{ $verifikasi->catatan_umum ?: 'Tidak ada catatan.' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Tidak ada data riwayat verifikasi berkas yang sesuai dengan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
