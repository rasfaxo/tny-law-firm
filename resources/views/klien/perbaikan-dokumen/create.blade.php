<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Dokumen Pengganti') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('klien.perbaikan-dokumen.store', $catatanVerifikasi) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <div class="rounded-md bg-gray-50 p-4 text-sm text-gray-700 space-y-2">
                        <div>
                            <span class="font-medium text-gray-900">{{ __('Pengajuan') }}:</span>
                            {{ $pengajuan->judul_perkara }}
                        </div>
                        <div>
                            <span class="font-medium text-gray-900">{{ __('Status Pengajuan') }}:</span>
                            {{ str_replace('_', ' ', ucfirst($pengajuan->status_pengajuan)) }}
                        </div>
                    </div>

                    <div class="rounded-md border border-gray-200 p-4 text-sm text-gray-700 space-y-2">
                        <h3 class="font-medium text-gray-900">{{ __('Dokumen yang Diperbaiki') }}</h3>
                        <div>
                            <span class="font-medium">{{ __('Nama Dokumen') }}:</span>
                            {{ $dokumen->nama_dokumen }}
                        </div>
                        <div>
                            <span class="font-medium">{{ __('Jenis Dokumen') }}:</span>
                            {{ $dokumen->jenis_dokumen }}
                        </div>
                        <div>
                            <span class="font-medium">{{ __('Status Dokumen') }}:</span>
                            {{ str_replace('_', ' ', ucfirst($dokumen->status_dokumen)) }}
                        </div>
                    </div>

                    <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800 space-y-2">
                        <h3 class="font-medium text-yellow-900">{{ __('Catatan Perbaikan') }}</h3>
                        <p class="whitespace-pre-line">{{ $catatanVerifikasi->isi_catatan }}</p>
                        <div>
                            {{ __('Status perbaikan') }}:
                            {{ str_replace('_', ' ', ucfirst($catatanVerifikasi->status_perbaikan)) }}
                        </div>
                    </div>

                    <div>
                        <x-input-label for="file" :value="__('File Dokumen Pengganti')" />
                        <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700" required>
                        <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('Format yang diizinkan: PDF, JPG, JPEG, PNG. Ukuran maksimal 5 MB.') }}
                        </p>
                    </div>

                    <div class="rounded-md bg-blue-50 p-4 text-sm text-blue-800">
                        {{ __('Dokumen lama tidak akan dihapus. Sistem akan membuat record dokumen baru dan menandai dokumen lama sebagai diganti.') }}
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>{{ __('Upload Dokumen Pengganti') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
