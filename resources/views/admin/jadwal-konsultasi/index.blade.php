<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                    <span>Admin</span>
                    <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-600">Slot Jadwal</span>
                </div>
                <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
                    {{ __('Kelola Slot Jadwal') }}
                </h2>
            </div>

            <a href="{{ route('admin.jadwal-konsultasi.create') }}" class="inline-flex items-center px-4 py-2.5 bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                <span>{{ __('Tambah Jadwal') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
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

        @if ($errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-800 rounded-xl p-4 text-xs font-semibold space-y-1">
                @foreach ($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <span class="h-1.5 w-1.5 rounded-full bg-rose-600 shrink-0"></span>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Pembuat</th>
                            <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($jadwalKonsultasi as $jadwal)
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-navy-dark text-sm">
                                    {{ $jadwal->tanggal?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                    {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$jadwal->status_slot" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-semibold">
                                    {{ $jadwal->admin?->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs">
                                    <div class="flex flex-wrap items-center justify-end gap-4">
                                        <a href="{{ route('admin.jadwal-konsultasi.show', $jadwal) }}" class="inline-flex items-center gap-1 font-bold text-navy-dark hover:text-accent-blue hover:underline transition">
                                            <span>Detail</span>
                                        </a>

                                        @if ($jadwal->status_slot !== 'terisi')
                                            <a href="{{ route('admin.jadwal-konsultasi.edit', $jadwal) }}" class="inline-flex items-center gap-1 font-bold text-accent-blue hover:underline transition">
                                                <span>Edit</span>
                                            </a>

                                            <form method="POST" action="{{ route('admin.jadwal-konsultasi.status', $jadwal) }}" class="inline-flex items-center gap-1.5">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status_slot" class="bg-[#F8FAFC] border-[#E2E8F0] text-xxs font-bold text-gray-600 rounded-lg px-2.5 py-1 focus:ring-accent-blue focus:border-accent-blue">
                                                    <option value="tersedia" @selected($jadwal->status_slot === 'tersedia')>Tersedia</option>
                                                    <option value="tidak_aktif" @selected($jadwal->status_slot === 'tidak_aktif')>Tidak Aktif</option>
                                                </select>
                                                <button type="submit" class="font-bold text-[#1e3a8a] hover:underline transition">
                                                    Ubah
                                                </button>
                                            </form>
                                        @else
                                            <span class="font-bold text-gray-400 select-none">{{ __('Terkunci (Terisi)') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-xs text-gray-400">
                                    Belum ada jadwal konsultasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($jadwalKonsultasi->hasPages())
                <div class="px-6 py-4 border-t border-[#E2E8F0]">
                    {{ $jadwalKonsultasi->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
