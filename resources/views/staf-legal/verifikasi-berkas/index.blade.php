<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Verifikasi Berkas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Klien</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($pengajuan as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item->judul_perkara }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $item->klien?->nama ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $item->kategori?->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                                {{ str_replace('_', ' ', ucfirst($item->status_pengajuan)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $item->dokumen_perkara_count }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $item->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('staf-legal.verifikasi-berkas.show', $item) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Detail') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada pengajuan yang perlu diverifikasi.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $pengajuan->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
