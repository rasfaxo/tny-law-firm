<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Booking Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perkara</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konfirmasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengajuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bookingKonsultasi as $booking)
                                    @php
                                        $pengajuan = $booking->praPendaftaranPerkara;
                                        $jadwal = $booking->jadwalKonsultasi;
                                        $metodeColor = $booking->metode_konsultasi === 'online' ? 'blue' : 'gray';
                                        $konfirmasiColor = $booking->status_konfirmasi_konsultasi === 'terkonfirmasi' ? 'green' : 'yellow';
                                        $bookingColor = match ($booking->status_booking) {
                                            'aktif' => 'green',
                                            'selesai' => 'blue',
                                            'dibatalkan' => 'red',
                                            default => 'gray',
                                        };
                                        $pengajuanColor = match ($pengajuan?->status_pengajuan) {
                                            'selesai' => 'green',
                                            'jadwal_dipilih' => 'blue',
                                            null => 'gray',
                                            default => 'yellow',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $booking->klien?->nama ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            <div class="font-medium text-gray-900">{{ $pengajuan?->judul_perkara ?? '-' }}</div>
                                            <div class="mt-1 text-sm text-gray-500">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <div>{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</div>
                                            <div class="mt-1 text-sm text-gray-500">
                                                {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) : '-' }}
                                                @if ($jadwal)
                                                    - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->metode_konsultasi ?? '-'" :color="$metodeColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi'" :color="$konfirmasiColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->status_booking" :color="$bookingColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$pengajuan?->status_pengajuan ?? '-'" :color="$pengajuanColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.booking-konsultasi.show', $booking) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Detail') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada booking konsultasi.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $bookingKonsultasi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
