<x-print-layout title="Laporan Reschedule Konsultasi">
    @php
        $statusRescheduleLabels = [
            'menunggu_persetujuan' => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
        ];
    @endphp

    <div class="space-y-6">
        <!-- Title Block -->
        <div class="text-center space-y-1">
            <h2 class="text-xl font-bold uppercase tracking-wide text-slate-900 underline font-serif">
                Laporan Permintaan Reschedule Konsultasi
            </h2>
            <p class="text-xs text-slate-600 font-medium">
                Rekapitulasi Permohonan Perubahan Jadwal Sesi Konsultasi oleh Klien • {{ config('firm.name', 'TNY & PARTNERS') }}
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
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Status / Metode</span>
                <span class="font-bold text-slate-900">
                    {{ !empty($filters['status_reschedule']) ? ($statusRescheduleLabels[$filters['status_reschedule']] ?? $filters['status_reschedule']) : 'Semua Status' }}
                    @if (!empty($filters['preferensi_metode']))
                        • {{ ucfirst($filters['preferensi_metode']) }}
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase tracking-wider">Total Data Ditemukan</span>
                <span class="font-bold text-slate-900">{{ $laporan->count() }} Permintaan</span>
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
                        <th class="border border-slate-400 px-3 py-2 text-left">Jadwal Lama</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Jadwal Baru / Preferensi</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-32">Status</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Alasan Permintaan</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-28">Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($laporan as $reschedule)
                        @php
                            $jadwalLama = $reschedule->bookingLama?->jadwalKonsultasi;
                            $jadwalBaru = $reschedule->jadwalBaru ?? $reschedule->bookingBaru?->jadwalKonsultasi;
                        @endphp
                        <tr>
                            <td class="border border-slate-400 px-2 py-2 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="border border-slate-400 px-3 py-2 font-mono font-bold text-blue-900 whitespace-nowrap">
                                RS-{{ str_pad($reschedule->id_reschedule, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">
                                {{ $reschedule->klien?->nama ?? 'Tidak diketahui' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-700">
                                @if ($jadwalLama)
                                    <div class="font-semibold">{{ $jadwalLama->tanggal?->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-600 font-mono">
                                        {{ substr((string) $jadwalLama->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalLama->waktu_selesai, 0, 5) }} WIB
                                    </div>
                                @else
                                    <span class="text-slate-500 italic">Tidak ditemukan</span>
                                @endif
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-900">
                                @if ($jadwalBaru)
                                    <div class="font-semibold text-emerald-900">{{ $jadwalBaru->tanggal?->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-600 font-mono">
                                        {{ substr((string) $jadwalBaru->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalBaru->waktu_selesai, 0, 5) }} WIB
                                    </div>
                                @elseif (!empty($reschedule->preferensi_jadwal))
                                    <div class="text-[11px] text-slate-800 font-medium">{{ $reschedule->preferensi_jadwal }}</div>
                                    <span class="text-[9.5px] text-slate-500 italic">(Preferensi Klien)</span>
                                @else
                                    <span class="text-slate-500 italic">Menunggu penjadwalan</span>
                                @endif
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold tracking-wide
                                    @if ($reschedule->status_reschedule === 'disetujui')
                                        bg-emerald-100 text-emerald-900 border border-emerald-400
                                    @elseif ($reschedule->status_reschedule === 'ditolak')
                                        bg-rose-100 text-rose-900 border border-rose-400
                                    @else
                                        bg-amber-100 text-amber-900 border border-amber-400
                                    @endif">
                                    {{ $statusRescheduleLabels[$reschedule->status_reschedule] ?? ucfirst(str_replace('_', ' ', $reschedule->status_reschedule)) }}
                                </span>
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-700 text-[11px]">
                                {{ $reschedule->alasan_reschedule ?: 'Belum diberikan.' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-600 font-mono text-[11px] whitespace-nowrap">
                                {{ $reschedule->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Tidak ada data permohonan reschedule yang sesuai dengan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
