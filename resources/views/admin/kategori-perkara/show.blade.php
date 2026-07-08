<x-app-layout title="Detail Kategori Perkara" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Kategori Perkara', 'url' => route('admin.kategori-perkara.index')], ['label' => 'Detail']]">

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <a href="{{ route('admin.kategori-perkara.index') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </a>

            <a href="{{ route('admin.kategori-perkara.edit', $kategoriPerkara) }}" class="inline-flex items-center px-4 py-2.5 bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>{{ __('Edit') }}</span>
            </a>
        </div>

        <div class="max-w-2xl mx-auto space-y-6">
            @if (session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-3">
                    <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-3">
                    <svg class="h-4 w-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
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
            </div>
        </div>
    </div>
</x-app-layout>
