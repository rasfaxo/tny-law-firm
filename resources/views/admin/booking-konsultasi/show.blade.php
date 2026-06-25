<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Booking Konsultasi') }}
        </h2>
    </x-slot>

    @php
        $pengajuan = $bookingKonsultasi->praPendaftaranPerkara;
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
        $metode = $bookingKonsultasi->metode_konsultasi ?? 'offline';
        $statusKonfirmasi = $bookingKonsultasi->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi';
        $metodeColor = $metode === 'online' ? 'blue' : 'gray';
        $konfirmasiColor = $statusKonfirmasi === 'terkonfirmasi' ? 'green' : 'yellow';
        $bookingColor = $bookingKonsultasi->status_booking === 'aktif' ? 'green' : 'gray';
        $canConfirm = $bookingKonsultasi->status_booking === 'aktif'
            && $pengajuan?->status_pengajuan === 'jadwal_dipilih';
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
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
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Data Klien') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nama</dt>
                                <dd class="mt-1 text-gray-900">{{ $bookingKonsultasi->klien?->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email</dt>
                                <dd class="mt-1 text-gray-900">{{ $bookingKonsultasi->klien?->email ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">No. Telepon</dt>
                                <dd class="mt-1 text-gray-900">{{ $bookingKonsultasi->klien?->no_telepon ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Data Pengajuan') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
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
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Data Jadwal') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Konsultasi</dt>
                                <dd class="mt-1 text-gray-900">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Waktu Konsultasi</dt>
                                <dd class="mt-1 text-gray-900">
                                    {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) : '-' }}
                                    @if ($jadwal)
                                        - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                    @endif
                                </dd>
                            </div>
                        </dl>
                    </div>

                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Informasi Konsultasi') }}</h3>
                        <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Booking</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$bookingKonsultasi->status_booking" :color="$bookingColor" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Metode Konsultasi</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$metode" :color="$metodeColor" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Status Konfirmasi</dt>
                                <dd class="mt-1">
                                    <x-status-badge :status="$statusKonfirmasi" :color="$konfirmasiColor" />
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tanggal Booking</dt>
                                <dd class="mt-1 text-gray-900">{{ $bookingKonsultasi->tanggal_booking?->format('d M Y H:i') ?? '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Catatan Preferensi Klien</dt>
                                <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $bookingKonsultasi->catatan_preferensi_klien ?: '-' }}</dd>
                            </div>

                            @if ($metode === 'online')
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Link Konsultasi</dt>
                                    <dd class="mt-1 text-gray-900">
                                        @if ($bookingKonsultasi->link_konsultasi)
                                            <a href="{{ $bookingKonsultasi->link_konsultasi }}" class="text-indigo-600 hover:text-indigo-900" target="_blank" rel="noopener noreferrer">
                                                {{ $bookingKonsultasi->link_konsultasi }}
                                            </a>
                                        @else
                                            {{ __('Belum tersedia') }}
                                        @endif
                                    </dd>
                                </div>
                            @else
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Lokasi Konsultasi</dt>
                                    <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $bookingKonsultasi->lokasi_konsultasi ?: __('Belum tersedia') }}</dd>
                                </div>
                            @endif

                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Catatan Konsultasi</dt>
                                <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $bookingKonsultasi->catatan_konsultasi ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Admin Konfirmasi</dt>
                                <dd class="mt-1 text-gray-900">{{ $bookingKonsultasi->adminKonfirmasi?->nama ?? '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Dikonfirmasi Pada</dt>
                                <dd class="mt-1 text-gray-900">{{ $bookingKonsultasi->dikonfirmasi_pada?->format('d M Y H:i') ?? '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('admin.booking-konsultasi.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali ke daftar booking') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Konfirmasi Detail Konsultasi') }}</h3>

                    @if ($canConfirm)
                        <form method="POST" action="{{ route('admin.booking-konsultasi.konfirmasi', $bookingKonsultasi) }}" class="mt-4 space-y-4">
                            @csrf
                            @method('PATCH')

                            @if ($metode === 'online')
                                <div>
                                    <x-input-label for="link_konsultasi" :value="__('Link Konsultasi')" />
                                    <x-text-input id="link_konsultasi" name="link_konsultasi" type="url" class="mt-1 block w-full" :value="old('link_konsultasi', $bookingKonsultasi->link_konsultasi)" placeholder="https://..." />
                                    <x-input-error :messages="$errors->get('link_konsultasi')" class="mt-2" />
                                </div>
                            @else
                                <div>
                                    <x-input-label for="lokasi_konsultasi" :value="__('Lokasi Konsultasi')" />
                                    <x-text-input id="lokasi_konsultasi" name="lokasi_konsultasi" type="text" class="mt-1 block w-full" :value="old('lokasi_konsultasi', $bookingKonsultasi->lokasi_konsultasi)" />
                                    <x-input-error :messages="$errors->get('lokasi_konsultasi')" class="mt-2" />
                                </div>
                            @endif

                            <div>
                                <x-input-label for="catatan_konsultasi" :value="__('Catatan Konsultasi')" />
                                <textarea id="catatan_konsultasi" name="catatan_konsultasi" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('catatan_konsultasi', $bookingKonsultasi->catatan_konsultasi) }}</textarea>
                                <x-input-error :messages="$errors->get('catatan_konsultasi')" class="mt-2" />
                            </div>

                            <x-primary-button>
                                {{ $statusKonfirmasi === 'terkonfirmasi' ? __('Perbarui Informasi Konsultasi') : __('Konfirmasi Konsultasi') }}
                            </x-primary-button>
                        </form>
                    @else
                        <p class="mt-4 text-sm text-gray-500">
                            {{ __('Booking ini tidak dapat dikonfirmasi karena booking tidak aktif atau pengajuan tidak berstatus jadwal dipilih.') }}
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
