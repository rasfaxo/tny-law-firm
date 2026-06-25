<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Permintaan Reschedule') }}
        </h2>
    </x-slot>

    @php
        $bookingLama = $permintaanReschedule->bookingLama;
        $bookingBaru = $permintaanReschedule->bookingBaru;
        $pengajuan = $bookingLama?->praPendaftaranPerkara;
        $jadwalLama = $bookingLama?->jadwalKonsultasi;
        $jadwalBaru = $permintaanReschedule->jadwalBaru ?? $bookingBaru?->jadwalKonsultasi;
        $statusColor = match ($permintaanReschedule->status_reschedule) {
            'disetujui' => 'green',
            'ditolak' => 'red',
            default => 'yellow',
        };
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Informasi Permintaan') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Reschedule</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$permintaanReschedule->status_reschedule" :color="$statusColor" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Pengajuan</dt>
                                <dd class="mt-1 text-gray-900">{{ $permintaanReschedule->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Alasan Reschedule</dt>
                                <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $permintaanReschedule->alasan_reschedule }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Preferensi Jadwal</dt>
                                <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $permintaanReschedule->preferensi_jadwal ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Preferensi Metode</dt>
                                <dd class="mt-1 text-gray-900">{{ $permintaanReschedule->preferensi_metode ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Keputusan</dt>
                                <dd class="mt-1 text-gray-900">{{ $permintaanReschedule->tanggal_keputusan?->format('d M Y H:i') ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Catatan Admin</dt>
                                <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $permintaanReschedule->catatan_admin ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    @if ($permintaanReschedule->status_reschedule === 'menunggu_persetujuan')
                        <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-700">
                            {{ __('Permintaan reschedule sedang menunggu persetujuan Admin. Jadwal lama tetap berlaku sampai Admin menyetujui perubahan.') }}
                        </div>
                    @elseif ($permintaanReschedule->status_reschedule === 'ditolak')
                        <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                            {{ __('Permintaan reschedule ditolak. Jadwal lama tetap berlaku.') }}
                        </div>
                    @elseif ($permintaanReschedule->status_reschedule === 'disetujui')
                        <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                            {{ __('Permintaan reschedule disetujui. Booking baru dibuat dan menunggu konfirmasi detail konsultasi dari Admin.') }}
                        </div>
                    @endif

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Booking Lama') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Judul Perkara</dt>
                                <dd class="mt-1 text-gray-900">{{ $pengajuan?->judul_perkara ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Jadwal Lama</dt>
                                <dd class="mt-1 text-gray-900">
                                    {{ $jadwalLama?->tanggal?->format('d M Y') ?? '-' }}
                                    @if ($jadwalLama)
                                        · {{ substr((string) $jadwalLama->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalLama->waktu_selesai, 0, 5) }}
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Booking Lama</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$bookingLama?->status_booking ?? '-'" :color="$bookingLama?->status_booking === 'aktif' ? 'green' : 'gray'" />
                                </dd>
                            </div>
                        </dl>
                    </div>

                    @if ($jadwalBaru || $bookingBaru)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Booking Baru') }}</h3>
                            <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Jadwal Baru</dt>
                                    <dd class="mt-1 text-gray-900">
                                        {{ $jadwalBaru?->tanggal?->format('d M Y') ?? '-' }}
                                        @if ($jadwalBaru)
                                            · {{ substr((string) $jadwalBaru->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalBaru->waktu_selesai, 0, 5) }}
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status Booking Baru</dt>
                                    <dd class="mt-1">
                                        <x-status-badge :status="$bookingBaru?->status_booking ?? '-'" :color="$bookingBaru?->status_booking === 'aktif' ? 'green' : 'gray'" />
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    @endif

                    <div class="pt-4">
                        <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali ke detail pengajuan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
