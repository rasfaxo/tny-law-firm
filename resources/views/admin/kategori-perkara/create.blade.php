<x-app-layout title="Tambah Kategori Perkara" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Kategori Perkara', 'url' => route('admin.kategori-perkara.index')], ['label' => 'Tambah']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <x-secondary-button href="{{ route('admin.kategori-perkara.index') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>
        </div>

        <div class="max-w-2xl mx-auto">
            <x-card>
                <form method="POST" action="{{ route('admin.kategori-perkara.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nama_kategori" :value="__('Nama Kategori')" />
                        <x-text-input id="nama_kategori" name="nama_kategori" type="text" class="mt-1 block w-full" :value="old('nama_kategori')" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nama_kategori')" />
                    </div>

                    <div>
                        <x-input-label for="deskripsi" :value="__('Deskripsi')" />
                        <x-text-input tag="textarea" id="deskripsi" name="deskripsi" rows="4" class="mt-1 block w-full px-4 py-3">{{ old('deskripsi') }}</x-text-input>
                        <x-input-error class="mt-2" :messages="$errors->get('deskripsi')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <x-secondary-button href="{{ route('admin.kategori-perkara.index') }}" tag="a">
                            {{ __('Batal') }}
                        </x-secondary-button>
                        <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
