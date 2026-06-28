<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Staf Legal Dashboard') }}
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
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Pengajuan yang Perlu Diverifikasi') }}</h3>
                        <a href="{{ route('staf-legal.verifikasi-berkas.index') }}" class="text-sm text-indigo-600 hover:text-indigo-900">
                            {{ __('Lihat Semua') }}
                        </a>
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuanPerluVerifikasi as $pengajuan)
                                    @php
                                        $statusColor = match ($pengajuan->status_pengajuan) {
                                            'menunggu_verifikasi_ulang' => 'orange',
                                            'berkas_tidak_lengkap' => 'red',
                                            'berkas_lengkap' => 'green',
                                            default => 'yellow',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan->klien?->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $pengajuan->judul_perkara }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan->kategori?->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$pengajuan->status_pengajuan" :color="$statusColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('staf-legal.verifikasi-berkas.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Detail') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Tidak ada pengajuan yang perlu diverifikasi.') }}
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
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Verifikasi Terakhir oleh Saya') }}</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Verifikasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Verifikasi</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan Umum</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($verifikasiTerakhir as $verifikasi)
                                    @php
                                        $statusColor = $verifikasi->status_verifikasi === 'berkas_lengkap' ? 'green' : 'red';
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 text-gray-700">{{ $verifikasi->praPendaftaranPerkara?->judul_perkara ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$verifikasi->status_verifikasi" :color="$statusColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $verifikasi->tanggal_verifikasi?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($verifikasi->catatan_umum ?? '-', 100) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada verifikasi yang Anda lakukan.') }}
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
