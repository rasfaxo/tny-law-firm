<x-print-layout title="Laporan Reschedule Konsultasi">
    @php
        $statusRescheduleLabels = [
            'menunggu' => 'Menunggu Persetujuan',
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
            <p class="text-xs text-slate-600">
                Rekapitulasi Permohonan Perubahan Jadwal Sesi Konsultasi oleh Klien
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
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Status / Metode</span>
                <span class="font-bold text-slate-900">
                    {{ $filters['status_reschedule'] ? ($statusRescheduleLabels[$filters['status_reschedule']] ?? $filters['status_reschedule']) : 'Semua Status' }}
                    @if ($filters['preferensi_metode'] ?? null)
                        • {{ ucfirst($filters['preferensi_metode']) }}
                    @endif
                </span>
            </div>
            <div>
                <span class="block text-[10px] font-semibold text-slate-500 uppercase">Total Data Ditemukan</span>
                <span class="font-bold text-slate-900">{{ $laporan->count() }} Permintaan</span>
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
                        <th class="border border-slate-400 px-3 py-2 text-left">Jadwal Lama</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Jadwal Baru Diajukan</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-28">Status</th>
                        <th class="border border-slate-400 px-3 py-2 text-left">Alasan Klien</th>
                        <th class="border border-slate-400 px-3 py-2 text-center w-24">Tgl Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($laporan as $reschedule)
                        <tr>
                            <td class="border border-slate-400 px-2 py-2 text-center text-slate-500">{{ $loop->iteration }}</td>
                            <td class="border border-slate-400 px-3 py-2 font-mono font-bold text-blue-900">
                                RS-{{ str_pad($reschedule->id_reschedule, 3, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 font-medium text-slate-900">
                                {{ $reschedule->klien?->nama ?? '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-700">
                                @if ($reschedule->bookingLama?->jadwalKonsultasi)
                                    <div>{{ $reschedule->bookingLama->jadwalKonsultasi->tanggal_konsultasi?->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $reschedule->bookingLama->jadwalKonsultasi->jam_mulai }} - {{ $reschedule->bookingLama->jadwalKonsultasi->jam_selesai }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-900 font-medium">
                                @if ($reschedule->bookingBaru?->jadwalKonsultasi)
                                    <div>{{ $reschedule->bookingBaru->jadwalKonsultasi->tanggal_konsultasi?->format('d/m/Y') }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono">{{ $reschedule->bookingBaru->jadwalKonsultasi->jam_mulai }} - {{ $reschedule->bookingBaru->jadwalKonsultasi->jam_selesai }}</div>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center font-semibold">
                                <span class="px-1.5 py-0.5 rounded text-[10.5px]
                                    @if ($reschedule->status_reschedule === 'disetujui')
                                        bg-emerald-100 text-emerald-800 border border-emerald-300
                                    @elseif ($reschedule->status_reschedule === 'ditolak')
                                        bg-rose-100 text-rose-800 border border-rose-300
                                    @else
                                        bg-amber-100 text-amber-800 border border-amber-300
                                    @endif">
                                    {{ $statusRescheduleLabels[$reschedule->status_reschedule] ?? $reschedule->status_reschedule }}
                                </span>
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-slate-700 text-[11px] italic">
                                {{ $reschedule->alasan ?: '-' }}
                            </td>
                            <td class="border border-slate-400 px-3 py-2 text-center text-slate-600 font-mono text-[11px]">
                                {{ $reschedule->tanggal_pengajuan?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="border border-slate-400 px-3 py-6 text-center text-slate-500 italic">
                                Tidak ada data permohonan reschedule yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-print-layout>
