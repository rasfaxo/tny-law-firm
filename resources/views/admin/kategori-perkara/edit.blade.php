<x-app-layout title="Edit Kategori Perkara" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Kategori Perkara', 'url' => route('admin.kategori-perkara.index')], ['label' => 'Edit']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <a href="{{ route('admin.kategori-perkara.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </a>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm">
                <form method="POST" action="{{ route('admin.kategori-perkara.update', $kategoriPerkara) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="nama_kategori" :value="__('Nama Kategori')" />
                        <x-text-input id="nama_kategori" name="nama_kategori" type="text" class="mt-1 block w-full" :value="old('nama_kategori', $kategoriPerkara->nama_kategori)" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_kategori')" />
                    </div>

                    <div>
                        <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('deskripsi', $kategoriPerkara->deskripsi) }}</textarea>
                        <x-input-error class="mt-2" :messages="$errors->get('deskripsi')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('admin.kategori-perkara.show', $kategoriPerkara) }}" class="text-sm text-gray-600 hover:text-gray-900 font-medium transition">
                            {{ __('Batal') }}
                        </a>
                        <x-primary-button class="bg-[#1e3a8a] hover:bg-blue-900 transition">{{ __('Simpan Perubahan') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
