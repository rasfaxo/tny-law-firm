<x-app-layout title="Detail Kategori Perkara" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Kategori Perkara', 'url' => route('admin.kategori-perkara.index')], ['label' => 'Detail']]">

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <x-secondary-button href="{{ route('admin.kategori-perkara.index') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>

            <x-primary-button href="{{ route('admin.kategori-perkara.edit', $kategoriPerkara) }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>{{ __('Edit') }}</span>
            </x-primary-button>
        </div>

        <div class="max-w-2xl mx-auto space-y-6">
            @if (session('success'))
                <x-alert-banner type="success">
                    {{ session('success') }}
                </x-alert-banner>
            @endif

            @if (session('error'))
                <x-alert-banner type="error">
                    {{ session('error') }}
                </x-alert-banner>
            @endif

            <x-card class="p-0 overflow-hidden">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Kategori</div>
                        <div class="mt-1 text-sm font-semibold text-navy-dark">{{ $kategoriPerkara->nama_kategori }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Deskripsi</div>
                        <div class="mt-1 text-sm text-[#475569] whitespace-pre-line leading-relaxed">{{ $kategoriPerkara->deskripsi ?: '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jumlah Pengajuan</div>
                        <div class="mt-1 text-sm font-semibold text-navy-dark">{{ $kategoriPerkara->pra_pendaftaran_perkara_count }}</div>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-[#F1F5F9]">
                        <form method="POST" action="{{ route('admin.kategori-perkara.destroy', $kategoriPerkara) }}" onsubmit="return confirm('Hapus kategori perkara ini?');">
                            @csrf
                            @method('DELETE')
                            <x-danger-button class="transition">{{ __('Hapus Kategori') }}</x-danger-button>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
