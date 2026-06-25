<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajukan Reschedule Konsultasi') }}
        </h2>
    </x-slot>

    @php
        $pengajuan = $bookingKonsultasi->praPendaftaranPerkara;
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
    @endphp

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                <div class="p-6 text-gray-900 space-y-4">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Booking Saat Ini') }}</h3>

                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Judul Perkara</dt>
                            <dd class="mt-1 text-gray-900">{{ $pengajuan?->judul_perkara ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                            <dd class="mt-1 text-gray-900">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</dd>
                        </div>
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
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Metode Saat Ini</dt>
                            <dd class="mt-1">
                                <x-status-badge :status="$bookingKonsultasi->metode_konsultasi ?? 'offline'" :color="($bookingKonsultasi->metode_konsultasi ?? 'offline') === 'online' ? 'blue' : 'gray'" />
                            </dd>
                        </div>
                    </dl>

                    <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-700">
                        {{ __('Booking lama tetap berlaku sampai Admin menyetujui permintaan reschedule.') }}
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('klien.permintaan-reschedule.store', $bookingKonsultasi) }}" class="space-y-4">
                        @csrf

                        <div>
                            <x-input-label for="alasan_reschedule" :value="__('Alasan Reschedule')" />
                            <textarea id="alasan_reschedule" name="alasan_reschedule" rows="5" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('alasan_reschedule') }}</textarea>
                            <x-input-error :messages="$errors->get('alasan_reschedule')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="preferensi_jadwal" :value="__('Preferensi Jadwal Baru')" />
                            <textarea id="preferensi_jadwal" name="preferensi_jadwal" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Saya lebih memungkinkan hari Jumat sore.">{{ old('preferensi_jadwal') }}</textarea>
                            <x-input-error :messages="$errors->get('preferensi_jadwal')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="preferensi_metode" :value="__('Preferensi Metode Baru')" />
                            <select id="preferensi_metode" name="preferensi_metode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">{{ __('Tetap gunakan metode lama') }}</option>
                                <option value="online" @selected(old('preferensi_metode') === 'online')>{{ __('Online') }}</option>
                                <option value="offline" @selected(old('preferensi_metode') === 'offline')>{{ __('Offline') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('preferensi_metode')" class="mt-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>
                                {{ __('Ajukan Reschedule') }}
                            </x-primary-button>

                            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
