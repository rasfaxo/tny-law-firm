<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Reschedule Konsultasi') }}
        </h2>
    </x-slot>

    <style>
        @media print {
            nav, header, .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-area { box-shadow: none !important; border: none !important; }
        }
    </style>

    @php
        $statusOptions = [
            'menunggu_persetujuan' => 'Menunggu Persetujuan',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
        ];
        $metodeOptions = [
            'online' => 'Online',
            'offline' => 'Offline',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="no-print bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('admin.laporan.reschedule-konsultasi') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="status_reschedule" class="block text-sm font-medium text-gray-700">Status Reschedule</label>
                            <select id="status_reschedule" name="status_reschedule" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['status_reschedule'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="preferensi_metode" class="block text-sm font-medium text-gray-700">Preferensi Metode</label>
                            <select id="preferensi_metode" name="preferensi_metode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Metode</option>
                                @foreach ($metodeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['preferensi_metode'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2 lg:col-span-4 flex flex-wrap items-center gap-3">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                {{ __('Terapkan Filter') }}
                            </button>
                            <a href="{{ route('admin.laporan.reschedule-konsultasi') }}" class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
                                {{ __('Reset Filter') }}
                            </a>
                            <button type="button" onclick="window.print()" class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                {{ __('Cetak') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="print-area bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Laporan Reschedule Konsultasi') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Jumlah data:') }} {{ $laporan->count() }}</p>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if (($filters['tanggal_mulai'] ?? null) || ($filters['tanggal_selesai'] ?? null))
                                Periode: {{ $filters['tanggal_mulai'] ?? 'awal' }} s/d {{ $filters['tanggal_selesai'] ?? 'akhir' }}
                            @else
                                Periode: Semua data
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID Reschedule</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nama Klien</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Jadwal Lama</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Preferensi Jadwal</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Preferensi Metode</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Keputusan</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Catatan Admin</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($laporan as $reschedule)
                                    @php
                                        $bookingLama = $reschedule->bookingLama;
                                        $jadwalLama = $bookingLama?->jadwalKonsultasi;
                                        $statusColor = match ($reschedule->status_reschedule) {
                                            'disetujui' => 'green',
                                            'ditolak' => 'red',
                                            default => 'yellow',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-3 text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $reschedule->id_reschedule }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $reschedule->klien?->nama ?? '-' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $bookingLama?->praPendaftaranPerkara?->judul_perkara ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">
                                            @if ($jadwalLama)
                                                {{ $jadwalLama->tanggal?->format('d M Y') ?? '-' }}, {{ $jadwalLama->waktu_mulai }} - {{ $jadwalLama->waktu_selesai }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($reschedule->preferensi_jadwal ?? '-', 100) }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">
                                            @if ($reschedule->preferensi_metode)
                                                <x-status-badge :status="$reschedule->preferensi_metode" :color="$reschedule->preferensi_metode === 'online' ? 'blue' : 'gray'" />
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$reschedule->status_reschedule" :color="$statusColor" />
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $reschedule->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $reschedule->tanggal_keputusan?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($reschedule->catatan_admin ?? '-', 100) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="px-3 py-8 text-center text-gray-500">
                                            {{ __('Tidak ada data reschedule sesuai filter.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
