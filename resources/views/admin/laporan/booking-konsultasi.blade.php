<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan Booking Konsultasi') }}
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
        $statusBookingOptions = [
            'aktif' => 'Aktif',
            'dibatalkan' => 'Dibatalkan',
            'selesai' => 'Selesai',
        ];
        $metodeOptions = [
            'online' => 'Online',
            'offline' => 'Offline',
        ];
        $statusKonfirmasiOptions = [
            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
            'terkonfirmasi' => 'Terkonfirmasi',
        ];
    @endphp

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="no-print bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('admin.laporan.booking-konsultasi') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="status_booking" class="block text-sm font-medium text-gray-700">Status Booking</label>
                            <select id="status_booking" name="status_booking" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                @foreach ($statusBookingOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['status_booking'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="metode_konsultasi" class="block text-sm font-medium text-gray-700">Metode Konsultasi</label>
                            <select id="metode_konsultasi" name="metode_konsultasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Metode</option>
                                @foreach ($metodeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['metode_konsultasi'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status_konfirmasi_konsultasi" class="block text-sm font-medium text-gray-700">Status Konfirmasi</label>
                            <select id="status_konfirmasi_konsultasi" name="status_konfirmasi_konsultasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                @foreach ($statusKonfirmasiOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['status_konfirmasi_konsultasi'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2 lg:col-span-5 flex flex-wrap items-center gap-3">
                            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                                {{ __('Terapkan Filter') }}
                            </button>
                            <a href="{{ route('admin.laporan.booking-konsultasi') }}" class="inline-flex items-center rounded-md bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200">
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
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Laporan Booking Konsultasi') }}</h3>
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
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID Booking</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nama Klien</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Kategori Perkara</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Jadwal</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Konfirmasi</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status Booking</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Booking</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($laporan as $booking)
                                    @php
                                        $pengajuan = $booking->praPendaftaranPerkara;
                                        $jadwal = $booking->jadwalKonsultasi;
                                        $bookingColor = match ($booking->status_booking) {
                                            'aktif' => 'green',
                                            'selesai' => 'blue',
                                            'dibatalkan' => 'red',
                                            default => 'gray',
                                        };
                                        $konfirmasiColor = $booking->status_konfirmasi_konsultasi === 'terkonfirmasi' ? 'green' : 'yellow';
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-3 text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $booking->id_booking }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $booking->klien?->nama ?? '-' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $pengajuan?->judul_perkara ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $jadwal?->waktu_mulai ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $jadwal?->waktu_selesai ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->metode_konsultasi ?? '-'" :color="$booking->metode_konsultasi === 'online' ? 'blue' : 'gray'" />
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi'" :color="$konfirmasiColor" />
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->status_booking" :color="$bookingColor" />
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $booking->tanggal_booking?->format('d M Y H:i') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="12" class="px-3 py-8 text-center text-gray-500">
                                            {{ __('Tidak ada data booking sesuai filter.') }}
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
