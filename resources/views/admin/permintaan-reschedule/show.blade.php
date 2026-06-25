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
        $canProcess = $permintaanReschedule->status_reschedule === 'menunggu_persetujuan';
    @endphp

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Data Klien dan Pengajuan') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama Klien</dt>
                                <dd class="mt-1 text-gray-900">{{ $permintaanReschedule->klien?->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Klien</dt>
                                <dd class="mt-1 text-gray-900">{{ $permintaanReschedule->klien?->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Judul Perkara</dt>
                                <dd class="mt-1 text-gray-900">{{ $pengajuan?->judul_perkara ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                                <dd class="mt-1 text-gray-900">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Pengajuan</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$pengajuan?->status_pengajuan ?? '-'" color="yellow" />
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Booking dan Jadwal Lama') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Konsultasi Lama</dt>
                                <dd class="mt-1 text-gray-900">{{ $jadwalLama?->tanggal?->format('d M Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Waktu Konsultasi Lama</dt>
                                <dd class="mt-1 text-gray-900">
                                    {{ $jadwalLama ? substr((string) $jadwalLama->waktu_mulai, 0, 5) : '-' }}
                                    @if ($jadwalLama)
                                        - {{ substr((string) $jadwalLama->waktu_selesai, 0, 5) }}
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Booking Lama</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$bookingLama?->status_booking ?? '-'" :color="$bookingLama?->status_booking === 'aktif' ? 'green' : 'gray'" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Metode Lama</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$bookingLama?->metode_konsultasi ?? 'offline'" :color="($bookingLama?->metode_konsultasi ?? 'offline') === 'online' ? 'blue' : 'gray'" />
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Permintaan Reschedule') }}</h3>
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

                    @if ($jadwalBaru || $bookingBaru)
                        <div class="border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Jadwal dan Booking Baru') }}</h3>
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
                        <a href="{{ route('admin.permintaan-reschedule.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali ke daftar permintaan') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Proses Permintaan Reschedule') }}</h3>

                    @if ($canProcess)
                        <div class="mt-4 grid grid-cols-1 gap-6 lg:grid-cols-2">
                            <form method="POST" action="{{ route('admin.permintaan-reschedule.setujui', $permintaanReschedule) }}" class="space-y-4 rounded-lg border border-gray-200 p-4">
                                @csrf
                                @method('PATCH')

                                <h4 class="font-medium text-gray-900">{{ __('Setujui Reschedule') }}</h4>

                                <div>
                                    <x-input-label for="id_jadwal_baru" :value="__('Jadwal Baru')" />
                                    <select id="id_jadwal_baru" name="id_jadwal_baru" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">{{ __('Pilih jadwal tersedia') }}</option>
                                        @foreach ($jadwalTersedia as $jadwal)
                                            <option value="{{ $jadwal->id_jadwal }}" @selected(old('id_jadwal_baru') == $jadwal->id_jadwal)>
                                                {{ $jadwal->tanggal?->format('d M Y') }} · {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('id_jadwal_baru')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="catatan_admin_approve" :value="__('Catatan Admin')" />
                                    <textarea id="catatan_admin_approve" name="catatan_admin" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('catatan_admin') }}</textarea>
                                    <x-input-error :messages="$errors->get('catatan_admin')" class="mt-2" />
                                </div>

                                <x-primary-button>
                                    {{ __('Setujui Reschedule') }}
                                </x-primary-button>
                            </form>

                            <form method="POST" action="{{ route('admin.permintaan-reschedule.tolak', $permintaanReschedule) }}" class="space-y-4 rounded-lg border border-gray-200 p-4">
                                @csrf
                                @method('PATCH')

                                <h4 class="font-medium text-gray-900">{{ __('Tolak Reschedule') }}</h4>

                                <div>
                                    <x-input-label for="catatan_admin_reject" :value="__('Catatan Admin')" />
                                    <textarea id="catatan_admin_reject" name="catatan_admin" rows="4" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('catatan_admin') }}</textarea>
                                    <x-input-error :messages="$errors->get('catatan_admin')" class="mt-2" />
                                </div>

                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Tolak Reschedule') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <p class="mt-4 text-sm text-gray-500">
                            {{ __('Permintaan reschedule ini sudah diproses dan tidak dapat diproses ulang.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
