<x-app-layout title="Kelola Kategori Perkara" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Kategori Perkara']]">

    <div class="space-y-6">
        <div class="flex justify-end">
            <x-primary-button href="{{ route('admin.kategori-perkara.create') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>{{ __('Tambah Kategori') }}</span>
            </x-primary-button>
        </div>
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
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama Kategori</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jumlah Pengajuan</th>
                            <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($kategoriPerkara as $kategori)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-navy-dark text-sm">
                                    {{ $kategori->nama_kategori }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 max-w-md truncate">
                                    {{ $kategori->deskripsi ?: '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-semibold">
                                    {{ $kategori->pra_pendaftaran_perkara_count }} pengajuan
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        <a href="{{ route('admin.kategori-perkara.show', $kategori) }}" class="inline-flex items-center gap-1 text-xs font-bold text-navy-dark hover:text-accent-blue hover:underline transition">
                                            <span>Detail</span>
                                        </a>
                                        <a href="{{ route('admin.kategori-perkara.edit', $kategori) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline transition">
                                            <span>Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.kategori-perkara.destroy', $kategori) }}" onsubmit="return confirm('Hapus kategori perkara ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-rose-600 hover:underline transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <x-empty-state title="Belum Ada Kategori Perkara" message="Belum ada kategori perkara yang ditambahkan." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($kategoriPerkara->hasPages())
                <div class="px-6 py-4 border-t border-[#E2E8F0]">
                    {{ $kategoriPerkara->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
