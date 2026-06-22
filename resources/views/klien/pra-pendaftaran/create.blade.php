<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Pra-Pendaftaran Perkara') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('klien.pra-pendaftaran.store') }}" class="p-6 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="id_kategori" :value="__('Kategori Perkara')" />
                        <select id="id_kategori" name="id_kategori" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="">Pilih kategori perkara</option>
                            @foreach ($kategoriPerkara as $kategori)
                                <option value="{{ $kategori->id_kategori }}" @selected(old('id_kategori') == $kategori->id_kategori)>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('id_kategori')" />
                    </div>

                    <div>
                        <x-input-label for="judul_perkara" :value="__('Judul Perkara')" />
                        <x-text-input id="judul_perkara" name="judul_perkara" type="text" class="mt-1 block w-full" :value="old('judul_perkara')" required />
                        <x-input-error class="mt-2" :messages="$errors->get('judul_perkara')" />
                    </div>

                    <div>
                        <x-input-label for="kronologi" :value="__('Kronologi')" />
                        <textarea id="kronologi" name="kronologi" rows="6" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('kronologi') }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('kronologi')" />
                    </div>

                    <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                        {{ __('Pengajuan yang sudah dikirim tidak dapat diedit. Upload dokumen akan dibuat pada fase berikutnya.') }}
                    </div>

                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('klien.pra-pendaftaran.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button>{{ __('Kirim Pengajuan') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
