<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pra-Pendaftaran Perkara') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Judul Perkara</div>
                        <div class="mt-1">{{ $praPendaftaranPerkara->judul_perkara }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Kategori Perkara</div>
                        <div class="mt-1">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Status Pengajuan</div>
                        <div class="mt-1">
                            <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                {{ str_replace('_', ' ', ucfirst($praPendaftaranPerkara->status_pengajuan)) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Tanggal Pengajuan</div>
                        <div class="mt-1">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Kronologi</div>
                        <div class="mt-1 whitespace-pre-line">{{ $praPendaftaranPerkara->kronologi }}</div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('klien.pra-pendaftaran.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali ke daftar') }}
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Riwayat Status') }}</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($praPendaftaranPerkara->riwayatStatus as $riwayat)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $riwayat->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ str_replace('_', ' ', ucfirst($riwayat->status)) }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $riwayat->keterangan ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $riwayat->user?->nama ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada riwayat status.') }}
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
