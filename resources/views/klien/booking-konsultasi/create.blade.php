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
                        {{ __('Pilih salah satu slot jadwal konsultasi yang tersedia untuk pengajuan ini.') }}
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Mulai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Selesai</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($jadwalKonsultasi as $jadwal)
                                    <tr>
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
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <form method="POST" action="{{ route('klien.booking-konsultasi.store', $praPendaftaranPerkara) }}">
                                                @csrf
                                                <input type="hidden" name="id_jadwal" value="{{ $jadwal->id_jadwal }}">
                                                <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Pilih Jadwal') }}
                                                </button>
                                            </form>
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

                    <div class="mt-6">
                        <a href="{{ route('klien.pra-pendaftaran.show', $praPendaftaranPerkara) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali ke detail pengajuan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
