<x-print-layout title="Laporan Rekapitulasi Sistem">
    <div class="space-y-6">
        <!-- Title & Metadata Block -->
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold uppercase tracking-wide text-slate-900 underline font-serif">
                Laporan Ringkasan & Rekapitulasi Pra-Pendaftaran Perkara
            </h2>
            <p class="text-xs text-slate-600 font-medium">
                Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web • {{ config('firm.name', 'TNY & PARTNERS') }}
            </p>
        </div>

        <!-- Filter & Print Metadata Info Box -->
        <div class="bg-slate-50 border border-slate-300 rounded-lg p-3.5 text-xs text-slate-700 grid grid-cols-2 sm:grid-cols-4 gap-3">
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Periode Laporan</span>
                <span class="font-bold text-slate-900">
                    @if (!empty($filters['tanggal_mulai']) || !empty($filters['tanggal_selesai']))
                        {{ !empty($filters['tanggal_mulai']) ? \Carbon\Carbon::parse($filters['tanggal_mulai'])->format('d/m/Y') : 'Awal' }} s/d {{ !empty($filters['tanggal_selesai']) ? \Carbon\Carbon::parse($filters['tanggal_selesai'])->format('d/m/Y') : 'Sekarang' }}
                    @else
                        Semua Periode (Keseluruhan)
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Pengajuan</span>
                <span class="font-bold text-slate-900">{{ $totalPengajuan }} Perkara</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Waktu Pencetakan</span>
                <span class="font-medium text-slate-800">{{ \Carbon\Carbon::now()->translatedFormat('d M Y, H:i') }} WIB</span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Operator Pencetak</span>
                <span class="font-medium text-slate-800">{{ auth()->user()->nama ?? 'Administrator' }}</span>
            </div>
        </div>

        <!-- Executive Summary Brief -->
        <div class="border-l-4 border-[#0F1E3A] bg-slate-50/80 p-3 rounded-r text-xs text-slate-700 leading-relaxed">
            <span class="font-bold text-slate-900 block mb-0.5">Ringkasan Eksekutif:</span>
            Pada periode laporan ini, tercatat akumulasi sebanyak <strong>{{ $totalPengajuan }} permohonan pra-pendaftaran perkara</strong>. Dari total permohonan tersebut, sebanyak <strong>{{ $berkasLengkap }} berkas</strong> telah dinyatakan lengkap dan memenuhi syarat verifikasi, <strong>{{ $bookingSelesai }} sesi konsultasi hukum</strong> telah tuntas diselenggarakan, dan <strong>{{ $pengajuanSelesai }} perkara</strong> telah menyelesaikan seluruh alur tahapan pra-pendaftaran secara tuntas.
        </div>

        <!-- Summary Metric Statistics -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">1. Ringkasan Status Pra-Pendaftaran</h3>
            <table class="w-full border-collapse border border-slate-400 text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-800 font-semibold">
                        <th class="border border-slate-400 px-3 py-2 text-left">Indikator Perkara</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-32">Jumlah</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    <tr>
                        <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">Total Seluruh Pengajuan Perkara Masuk</td>
                        <td class="border border-slate-400 px-3 py-2 text-center font-bold text-blue-900">{{ $totalPengajuan }}</td>
                        <td class="border border-slate-400 px-3 py-2 text-slate-600">Seluruh permohonan yang diajukan oleh Klien</td>
                    </tr>
                    <tr>
                        <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">Berkas Telah Lengkap & Terverifikasi</td>
                        <td class="border border-slate-400 px-3 py-2 text-center font-bold text-emerald-800">{{ $berkasLengkap }}</td>
                        <td class="border border-slate-400 px-3 py-2 text-slate-600">Berkas telah memenuhi syarat dan siap konsultasi</td>
                    </tr>
                    <tr>
                        <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">Sesi Konsultasi Telah Selesai</td>
                        <td class="border border-slate-400 px-3 py-2 text-center font-bold text-indigo-900">{{ $bookingSelesai }}</td>
                        <td class="border border-slate-400 px-3 py-2 text-slate-600">Sesi tatap muka / daring telah tuntas dilaksanakan</td>
                    </tr>
                    <tr>
                        <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">Perkara Telah Selesai (Tuntas)</td>
                        <td class="border border-slate-400 px-3 py-2 text-center font-bold text-emerald-900">{{ $pengajuanSelesai }}</td>
                        <td class="border border-slate-400 px-3 py-2 text-slate-600">Seluruh rangkaian tahapan pra-pendaftaran tuntas</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Summary by Category -->
        <div>
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800 mb-2">2. Distribusi Pengajuan per Kategori Perkara</h3>
            <table class="w-full border-collapse border border-slate-400 text-xs">
                <thead>
                    <tr class="bg-slate-100 text-slate-800 font-semibold">
                        <th class="border border-slate-400 px-3 py-2 text-center w-10">No</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Kategori Perkara</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-20">Total</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-24">Berkas Lengkap</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-24">Jadwal Dipilih</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-20">Selesai</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Deskripsi / Ruang Lingkup</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($kategoriSummary as $summary)
                        <tr>
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="border border-slate-400 px-3 py-2 font-bold text-slate-900">{{ $summary->nama_kategori }}</td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-bold text-blue-900">{{ $summary->total_count }}</td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold text-slate-800">{{ $summary->berkas_lengkap_count }}</td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold text-slate-800">{{ $summary->jadwal_dipilih_count }}</td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold text-slate-800">{{ $summary->selesai_count }}</td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-600 text-[11px]">{{ $summary->deskripsi ?: 'Tidak ada deskripsi khusus.' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Belum ada data kategori perkara yang tercatat dalam sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
