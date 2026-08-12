<x-app-layout title="Data Klien" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Data Klien']]">

    <div class="space-y-6">
        <!-- Filter & Search Bar -->
        <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm">
            <form method="GET" action="{{ route('admin.klien.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <!-- Search Input -->
                <div class="md:col-span-11 space-y-1.5">
                    <label for="search" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Cari Klien</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." 
                               class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 pl-4 pr-10 py-2">
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
                    <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm h-full w-full rounded-xl flex items-center justify-center transition shadow-md shadow-blue-900/20" title="Cari">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-sm font-semibold flex items-center gap-3">
                <svg class="h-5 w-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
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
                                        <div class="h-8 w-8 rounded-full bg-blue-50 text-[#1e3a8a] border border-blue-100 flex items-center justify-center font-bold text-xs shrink-0">
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
                                    @if($user->status_akun === 'aktif')
                                        <span class="inline-flex rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-100 border border-rose-200 px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider text-rose-800">
                                            Nonaktif
                                        </span>
                                    @endif
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
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="bg-gray-50 p-4 rounded-full w-14 h-14 mx-auto flex items-center justify-center text-gray-400">
                                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </div>
                                        <p class="text-sm font-semibold text-navy-dark">Tidak Ada Data Klien</p>
                                    </div>
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
                                <div class="h-8 w-8 rounded-full bg-blue-50 text-[#1e3a8a] border border-blue-100 flex items-center justify-center font-bold text-xs shrink-0">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                                <div class="font-bold text-navy-dark text-sm">
                                    {{ $user->nama }}
                                </div>
                            </div>
                            @if($user->status_akun === 'aktif')
                                <span class="inline-flex rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider text-emerald-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-rose-100 border border-rose-200 px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider text-rose-800">
                                    Nonaktif
                                </span>
                            @endif
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
                        Tidak ada data Klien.
                    </div>
                @endforelse
            </div>

            @if ($klien->hasPages())
                <div class="px-6 py-4 border-t border-[#E2E8F0] bg-[#F8FAFC]">
                    {{ $klien->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
