<x-app-layout title="Data Klien" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Data Klien']]">

    <div class="space-y-6">
        <!-- Filter & Search Bar -->
        <x-card>
            <form method="GET" action="{{ route('admin.klien.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search Input -->
                <div class="md:col-span-11 space-y-1.5">
                    <x-input-label for="search" :value="__('Cari Klien')" class="!text-xs !font-bold !text-gray-400 !uppercase !tracking-wider" />
                    <div class="relative">
                        <x-text-input type="text" name="search" id="search" :value="request('search')" placeholder="Cari nama atau email..." class="w-full pr-10" />
                        @if(request('search'))
                            <a href="{{ route('admin.klien.index') }}" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="md:col-span-1 flex items-center justify-end h-11">
                    <x-primary-button class="h-full w-full justify-center px-0" title="Cari">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </x-primary-button>
                </div>
            </form>
        </x-card>

        @if (session('success'))
            <x-alert-banner type="success">
                {{ session('success') }}
            </x-alert-banner>
        @endif

        <x-card class="p-0 overflow-hidden">
            <!-- Desktop Table Layout -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Klien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">No. Telepon</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($klien as $user)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-50 text-accent-blue border border-blue-100 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                                        </div>
                                        <div class="font-bold text-navy-dark text-sm">
                                            {{ $user->nama }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                                    {{ $user->no_telepon ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$user->status_akun" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        <a href="{{ route('admin.klien.show', $user) }}" class="inline-flex items-center gap-1 text-sm font-bold text-navy-dark hover:text-accent-blue hover:underline transition">
                                            <span>Detail</span>
                                        </a>
                                        <a href="{{ route('admin.klien.edit', $user) }}" class="inline-flex items-center gap-1 text-sm font-bold text-accent-blue hover:underline transition">
                                            <span>Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.klien.status', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_akun" value="{{ $user->status_akun === 'aktif' ? 'nonaktif' : 'aktif' }}">
                                            @if($user->status_akun === 'aktif')
                                                <button type="submit" class="text-sm font-bold text-rose-600 hover:underline transition">
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="text-sm font-bold text-emerald-600 hover:underline transition">
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <x-empty-state title="Tidak Ada Data Klien" message="Tidak ditemukan klien yang cocok dengan kriteria pencarian Anda." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout -->
            <div class="block md:hidden divide-y divide-[#F1F5F9] bg-white">
                @forelse ($klien as $user)
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-full bg-blue-50 text-accent-blue border border-blue-100 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                                <div class="font-bold text-navy-dark text-sm">
                                    {{ $user->nama }}
                                </div>
                            </div>
                            <x-status-badge :status="$user->status_akun" />
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mt-1"><span class="font-medium">Email:</span> {{ $user->email }}</p>
                            <p class="text-xs text-gray-500 mt-0.5"><span class="font-medium">Telp:</span> {{ $user->no_telepon ?? '-' }}</p>
                        </div>
                        <div class="flex justify-end items-center gap-4 pt-2 border-t border-gray-100">
                            <a href="{{ route('admin.klien.show', $user) }}" class="inline-flex items-center gap-1 text-xs font-bold text-navy-dark hover:underline">
                                Detail
                            </a>
                            <a href="{{ route('admin.klien.edit', $user) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.klien.status', $user) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status_akun" value="{{ $user->status_akun === 'aktif' ? 'nonaktif' : 'aktif' }}">
                                @if($user->status_akun === 'aktif')
                                    <button type="submit" class="text-xs font-bold text-rose-600 hover:underline">
                                        Nonaktifkan
                                    </button>
                                @else
                                    <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline">
                                        Aktifkan
                                    </button>
                                @endif
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-gray-400">
                        <x-empty-state title="Tidak Ada Data Klien" message="Tidak ditemukan klien yang cocok dengan kriteria pencarian Anda." />
                    </div>
                @endforelse
            </div>

            @if ($klien->hasPages())
                <div class="px-6 py-4 border-t border-[#E2E8F0] bg-[#F8FAFC]">
                    {{ $klien->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
