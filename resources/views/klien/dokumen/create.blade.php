<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Upload Dokumen Perkara') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('klien.dokumen.store', $praPendaftaranPerkara) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <div class="rounded-md bg-gray-50 p-4 text-sm text-gray-700">
                        <div class="font-medium text-gray-900">{{ $praPendaftaranPerkara->judul_perkara }}</div>
                        <div class="mt-1">
                            {{ __('Status') }}: {{ str_replace('_', ' ', ucfirst($praPendaftaranPerkara->status_pengajuan)) }}
                        </div>
                    </div>

                    <div>
                        <x-input-label for="nama_dokumen" :value="__('Nama Dokumen')" />
                        <x-text-input id="nama_dokumen" name="nama_dokumen" type="text" class="mt-1 block w-full" :value="old('nama_dokumen')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_dokumen')" />
                    </div>

                    <div>
                        <x-input-label for="jenis_dokumen" :value="__('Jenis Dokumen')" />
                        <x-text-input id="jenis_dokumen" name="jenis_dokumen" type="text" class="mt-1 block w-full" :value="old('jenis_dokumen')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('jenis_dokumen')" />
                    </div>

                    <div>
                        <x-input-label for="file" :value="__('File Dokumen')" />
                        <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" class="mt-1 block w-full text-sm text-gray-700 file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700" required>
                        <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        <p class="mt-2 text-sm text-gray-500">
                            {{ __('Format yang diizinkan: PDF, JPG, JPEG, PNG. Ukuran maksimal 5 MB.') }}
                        </p>
                    </div>

                    <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                        {{ __('File disimpan dengan nama unik oleh sistem. Nama file asli tidak digunakan sebagai nama final.') }}
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('klien.pra-pendaftaran.show', $praPendaftaranPerkara) }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>{{ __('Upload Dokumen') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
