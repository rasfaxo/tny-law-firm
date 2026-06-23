<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Verifikasi Berkas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <div class="font-medium">{{ __('Data verifikasi belum valid.') }}</div>
                    <ul class="mt-2 list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <div class="text-sm font-medium text-gray-500">Judul Perkara</div>
                            <div class="mt-1">{{ $praPendaftaranPerkara->judul_perkara }}</div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-500">Kategori Perkara</div>
                            <div class="mt-1">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-500">Nama Klien</div>
                            <div class="mt-1">{{ $praPendaftaranPerkara->klien?->nama ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-500">Email Klien</div>
                            <div class="mt-1">{{ $praPendaftaranPerkara->klien?->email ?? '-' }}</div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-500">Status Pengajuan</div>
                            <div class="mt-1">
                                <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" color="yellow" />
                            </div>
                        </div>

                        <div>
                            <div class="text-sm font-medium text-gray-500">Tanggal Pengajuan</div>
                            <div class="mt-1">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</div>
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Kronologi</div>
                        <div class="mt-1 whitespace-pre-line">{{ $praPendaftaranPerkara->kronologi }}</div>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('staf-legal.verifikasi-berkas.store', $praPendaftaranPerkara) }}" class="space-y-6">
                @csrf

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Dokumen Aktif') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Buka dokumen melalui link aman, lalu tetapkan status valid atau perlu perbaikan.') }}
                        </p>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Saat Ini</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hasil Verifikasi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                                        @php
                                            $oldStatus = old("dokumen.{$dokumen->id_dokumen}.status_dokumen", $dokumen->status_dokumen === 'perlu_perbaikan' ? 'perlu_perbaikan' : 'valid');
                                        @endphp
                                        <tr class="align-top">
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $dokumen->nama_dokumen }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->jenis_dokumen }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                <x-status-badge :status="$dokumen->status_dokumen" />
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Lihat/Unduh') }}
                                                </a>
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 min-w-72">
                                                <div class="space-y-3">
                                                    <div class="flex flex-wrap gap-4">
                                                        <label class="inline-flex items-center gap-2 text-sm">
                                                            <input type="radio" name="dokumen[{{ $dokumen->id_dokumen }}][status_dokumen]" value="valid" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked($oldStatus === 'valid')>
                                                            <span>{{ __('Valid') }}</span>
                                                        </label>
                                                        <label class="inline-flex items-center gap-2 text-sm">
                                                            <input type="radio" name="dokumen[{{ $dokumen->id_dokumen }}][status_dokumen]" value="perlu_perbaikan" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked($oldStatus === 'perlu_perbaikan')>
                                                            <span>{{ __('Perlu Perbaikan') }}</span>
                                                        </label>
                                                    </div>

                                                    <textarea name="dokumen[{{ $dokumen->id_dokumen }}][catatan]" rows="3" class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Catatan perbaikan untuk dokumen ini">{{ old("dokumen.{$dokumen->id_dokumen}.catatan") }}</textarea>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                {{ __('Belum ada dokumen aktif untuk diverifikasi.') }}
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
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Riwayat Dokumen') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Dokumen yang sudah diganti ditampilkan sebagai riwayat read-only dan tidak ikut proses verifikasi utama.') }}
                        </p>

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
                                    @forelse ($praPendaftaranPerkara->riwayatDokumen as $dokumen)
                                        <tr>
                                            <td class="px-4 py-3 font-medium text-gray-900">{{ $dokumen->nama_dokumen }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->jenis_dokumen }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                <x-status-badge :status="$dokumen->status_dokumen" color="gray" />
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ __('Lihat/Unduh') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                {{ __('Belum ada riwayat dokumen.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 space-y-6">
                        <div>
                            <x-input-label :value="__('Status Verifikasi')" />
                            <div class="mt-2 flex flex-wrap gap-4">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="radio" name="status_verifikasi" value="berkas_lengkap" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('status_verifikasi', 'berkas_lengkap') === 'berkas_lengkap')>
                                    <span>{{ __('Berkas Lengkap') }}</span>
                                </label>
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="radio" name="status_verifikasi" value="berkas_tidak_lengkap" class="border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('status_verifikasi') === 'berkas_tidak_lengkap')>
                                    <span>{{ __('Berkas Tidak Lengkap') }}</span>
                                </label>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('status_verifikasi')" />
                        </div>

                        <div>
                            <x-input-label for="catatan_umum" :value="__('Catatan Umum Verifikasi')" />
                            <textarea id="catatan_umum" name="catatan_umum" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('catatan_umum') }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('catatan_umum')" />
                        </div>

                        <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                            {{ __('Jika memilih Berkas Tidak Lengkap, minimal satu dokumen harus diberi status Perlu Perbaikan dan memiliki catatan perbaikan.') }}
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('staf-legal.verifikasi-berkas.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                                {{ __('Kembali') }}
                            </a>
                            <x-primary-button>{{ __('Simpan Verifikasi') }}</x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
