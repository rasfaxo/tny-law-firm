<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Klien Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($statistics as $label => $value)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-5">
                            <div class="text-sm font-medium text-gray-500">{{ $label }}</div>
                            <div class="mt-2 text-3xl font-semibold text-gray-900">{{ $value }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Pengajuan Saya Terbaru') }}</h3>
                        <a href="{{ route('klien.pra-pendaftaran.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            {{ __('Lihat Semua') }}
                        </a>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuanTerbaru as $pengajuan)
                                    @php
                                        $statusColor = match ($pengajuan->status_pengajuan) {
                                            'selesai' => 'green',
                                            'jadwal_dipilih', 'berkas_lengkap' => 'blue',
                                            'berkas_tidak_lengkap' => 'red',
                                            'menunggu_verifikasi_ulang' => 'orange',
                                            default => 'yellow',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-gray-700">{{ $pengajuan->judul_perkara }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan->kategori?->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$pengajuan->status_pengajuan" :color="$statusColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Detail') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada pengajuan pra-pendaftaran.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Booking Konsultasi Aktif') }}</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Jadwal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konfirmasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($bookingAktif as $booking)
                                    @php
                                        $jadwal = $booking->jadwalKonsultasi;
                                        $konfirmasiColor = $booking->status_konfirmasi_konsultasi === 'terkonfirmasi' ? 'green' : 'yellow';
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-gray-700">
                                            <a href="{{ route('klien.pra-pendaftaran.show', $booking->praPendaftaranPerkara) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $booking->praPendaftaranPerkara?->judul_perkara ?? '-' }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $jadwal ? $jadwal->waktu_mulai . ' - ' . $jadwal->waktu_selesai : '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->metode_konsultasi ?? '-'" :color="$booking->metode_konsultasi === 'online' ? 'blue' : 'gray'" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi'" :color="$konfirmasiColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$booking->status_booking" color="green" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Tidak ada booking konsultasi aktif.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Permintaan Reschedule Saya') }}</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan Admin</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($permintaanRescheduleSaya as $reschedule)
                                    @php
                                        $statusColor = match ($reschedule->status_reschedule) {
                                            'disetujui' => 'green',
                                            'ditolak' => 'red',
                                            default => 'yellow',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-gray-700">{{ $reschedule->bookingLama?->praPendaftaranPerkara?->judul_perkara ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$reschedule->status_reschedule" :color="$statusColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $reschedule->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($reschedule->catatan_admin ?? '-', 100) }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('klien.permintaan-reschedule.show', $reschedule) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Detail') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada permintaan reschedule.') }}
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
