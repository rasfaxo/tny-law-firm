<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pilih Jadwal Konsultasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                <div class="p-6 text-gray-900 space-y-2">
                    <h3 class="text-lg font-medium text-gray-900">{{ $praPendaftaranPerkara->judul_perkara }}</h3>
                    <p class="text-sm text-gray-500">
                        {{ __('Pilih metode konsultasi dan salah satu slot jadwal yang tersedia untuk pengajuan ini.') }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('klien.booking-konsultasi.store', $praPendaftaranPerkara) }}" class="space-y-6">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-4">
                        <div>
                            <x-input-label for="metode_konsultasi" :value="__('Metode Konsultasi')" />
                            <div class="mt-2 flex flex-wrap gap-4">
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input type="radio" name="metode_konsultasi" value="online" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('metode_konsultasi') === 'online')>
                                    {{ __('Online') }}
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm text-gray-700">
                                    <input type="radio" name="metode_konsultasi" value="offline" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('metode_konsultasi', 'offline') === 'offline')>
                                    {{ __('Offline') }}
                                </label>
                            </div>
                            <x-input-error :messages="$errors->get('metode_konsultasi')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="catatan_preferensi_klien" :value="__('Catatan Preferensi Klien')" />
                            <textarea id="catatan_preferensi_klien" name="catatan_preferensi_klien" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Contoh: Saya berada di luar kota, lebih nyaman online.') }}">{{ old('catatan_preferensi_klien') }}</textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Opsional. Jelaskan preferensi atau kebutuhan teknis konsultasi Anda.') }}
                            </p>
                            <x-input-error :messages="$errors->get('catatan_preferensi_klien')" class="mt-2" />
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Slot Jadwal Tersedia') }}</h3>
                        <x-input-error :messages="$errors->get('id_jadwal')" class="mt-2" />

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pilih</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($jadwalKonsultasi as $jadwal)
                                        <tr>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                <input type="radio" name="id_jadwal" value="{{ $jadwal->id_jadwal }}" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked((string) old('id_jadwal') === (string) $jadwal->id_jadwal)>
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {{ $jadwal->tanggal?->format('d M Y') ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {{ substr((string) $jadwal->waktu_mulai, 0, 5) }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                <x-status-badge :status="$jadwal->status_slot" color="green" />
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                {{ __('Belum ada slot jadwal konsultasi yang tersedia.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6">
                            {{ $jadwalKonsultasi->links() }}
                        </div>

                        <div class="mt-6 flex items-center justify-between gap-4">
                            <a href="{{ route('klien.pra-pendaftaran.show', $praPendaftaranPerkara) }}" class="text-sm text-gray-600 hover:text-gray-900">
                                {{ __('Kembali ke detail pengajuan') }}
                            </a>

                            <x-primary-button>
                                {{ __('Pilih Jadwal') }}
                            </x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
