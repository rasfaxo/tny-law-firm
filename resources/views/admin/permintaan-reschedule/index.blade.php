<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Permintaan Reschedule') }}
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
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jadwal Lama</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferensi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($permintaanReschedule as $permintaan)
                                    @php
                                        $booking = $permintaan->bookingLama;
                                        $pengajuan = $booking?->praPendaftaranPerkara;
                                        $jadwal = $booking?->jadwalKonsultasi;
                                        $statusColor = match ($permintaan->status_reschedule) {
                                            'disetujui' => 'green',
                                            'ditolak' => 'red',
                                            default => 'yellow',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $permintaan->klien?->nama ?? '-' }}
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
                                        <td class="px-4 py-3 text-gray-700">
                                            <div class="max-w-xs truncate">{{ $permintaan->preferensi_jadwal ?: '-' }}</div>
                                            <div class="mt-1 text-sm text-gray-500">{{ $permintaan->preferensi_metode ?: 'Metode lama' }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$permintaan->status_reschedule" :color="$statusColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $permintaan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('admin.permintaan-reschedule.show', $permintaan) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Detail') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada permintaan reschedule.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $permintaanReschedule->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
