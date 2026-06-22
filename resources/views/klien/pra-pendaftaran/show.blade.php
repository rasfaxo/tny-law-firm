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

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
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
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Dokumen Perkara') }}</h3>

                        @if ($praPendaftaranPerkara->status_pengajuan === 'menunggu_verifikasi')
                            <a href="{{ route('klien.dokumen.create', $praPendaftaranPerkara) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Upload Dokumen') }}
                            </a>
                        @endif
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($praPendaftaranPerkara->dokumenPerkara as $dokumen)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $dokumen->nama_dokumen }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->jenis_dokumen }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <span class="inline-flex rounded-full bg-blue-100 px-2 text-xs font-semibold leading-5 text-blue-800">
                                                {{ str_replace('_', ' ', ucfirst($dokumen->status_dokumen)) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('klien.dokumen.show', $dokumen) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Lihat/Unduh') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada dokumen perkara yang diunggah.') }}
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
