<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Pra-Pendaftaran Saya') }}
            </h2>

            <a href="{{ route('klien.pra-pendaftaran.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Buat Pengajuan') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
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
                                @forelse ($praPendaftaranPerkara as $pengajuan)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $pengajuan->judul_perkara }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan->kategori?->nama_kategori ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <span class="inline-flex rounded-full bg-yellow-100 px-2 text-xs font-semibold leading-5 text-yellow-800">
                                                {{ str_replace('_', ' ', ucfirst($pengajuan->status_pengajuan)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $pengajuan->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada pra-pendaftaran perkara.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $praPendaftaranPerkara->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
