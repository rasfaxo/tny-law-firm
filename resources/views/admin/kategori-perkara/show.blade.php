<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Kategori Perkara') }}
            </h2>

            <a href="{{ route('admin.kategori-perkara.edit', $kategoriPerkara) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
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
                        <div class="text-sm font-medium text-gray-500">Nama Kategori</div>
                        <div class="mt-1">{{ $kategoriPerkara->nama_kategori }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Deskripsi</div>
                        <div class="mt-1 whitespace-pre-line">{{ $kategoriPerkara->deskripsi ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Jumlah Pengajuan</div>
                        <div class="mt-1">{{ $kategoriPerkara->pra_pendaftaran_perkara_count }}</div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('admin.kategori-perkara.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali') }}
                        </a>

                        <form method="POST" action="{{ route('admin.kategori-perkara.destroy', $kategoriPerkara) }}" onsubmit="return confirm('Hapus kategori perkara ini?');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button>{{ __('Hapus') }}</x-danger-button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
