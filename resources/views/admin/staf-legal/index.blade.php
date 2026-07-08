<x-app-layout title="Kelola Staf Legal" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Staf Legal']]">

    <div class="space-y-6">
        <div class="flex justify-end">
            <a href="{{ route('admin.staf-legal.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>{{ __('Tambah Staf Legal') }}</span>
            </a>
        </div>
        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 text-xs font-semibold flex items-center gap-3">
                <svg class="h-4 w-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">No. Telepon</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($stafLegal as $user)
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
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ $user->no_telepon ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->status_akun === 'aktif')
                                        <span class="inline-flex rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 text-xxs font-extrabold uppercase tracking-wider text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-100 border border-rose-200 px-2.5 py-0.5 text-xxs font-extrabold uppercase tracking-wider text-rose-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end items-center gap-4">
                                        <a href="{{ route('admin.staf-legal.show', $user) }}" class="inline-flex items-center gap-1 text-xs font-bold text-navy-dark hover:text-accent-blue hover:underline transition">
                                            <span>Detail</span>
                                        </a>
                                        <a href="{{ route('admin.staf-legal.edit', $user) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline transition">
                                            <span>Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.staf-legal.status', $user) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_akun" value="{{ $user->status_akun === 'aktif' ? 'nonaktif' : 'aktif' }}">
                                            @if($user->status_akun === 'aktif')
                                                <button type="submit" class="text-xs font-bold text-rose-600 hover:underline transition">
                                                    Nonaktifkan
                                                </button>
                                            @else
                                                <button type="submit" class="text-xs font-bold text-emerald-600 hover:underline transition">
                                                    Aktifkan
                                                </button>
                                            @endif
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-xs text-gray-400">
                                    Belum ada akun Staf Legal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($stafLegal->hasPages())
                <div class="px-6 py-4 border-t border-[#E2E8F0]">
                    {{ $stafLegal->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
