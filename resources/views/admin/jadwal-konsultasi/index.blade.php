<x-app-layout title="Kelola Slot Jadwal" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Slot Jadwal']]">

    <div class="space-y-6">
        <div class="flex justify-end">
            <x-primary-button href="{{ route('admin.jadwal-konsultasi.create') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2zm7-7v4m-2-2h4"></path>
                </svg>
                <span>{{ __('Tambah Jadwal') }}</span>
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

        <x-card class="p-0 sm:p-0">
            <div class="w-full">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Pembuat</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
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
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="inline-flex items-center justify-center h-8 w-8 text-gray-400 hover:text-navy-dark hover:bg-gray-100 rounded-lg transition">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                </svg>
                                            </button>
                                        </x-slot>
                                        <x-slot name="content">
                                            <x-dropdown-link href="{{ route('admin.jadwal-konsultasi.show', $jadwal) }}" class="text-xs font-semibold">
                                                Detail
                                            </x-dropdown-link>

                                            @if ($jadwal->status_slot !== 'terisi')
                                                <x-dropdown-link href="{{ route('admin.jadwal-konsultasi.edit', $jadwal) }}" class="text-xs font-semibold">
                                                    Edit
                                                </x-dropdown-link>
                                            @else
                                                <div class="block w-full px-4 py-2 text-left text-xs font-semibold text-gray-300 cursor-not-allowed bg-gray-50/50" title="Terkunci (Terisi)">
                                                    Edit
                                                </div>
                                            @endif

                                            <div class="border-t border-[#E2E8F0] my-1"></div>

                                            <div class="px-4 py-1.5 text-xs font-bold text-gray-400 uppercase tracking-wider">
                                                Ubah Status
                                            </div>

                                            <form method="POST" action="{{ route('admin.jadwal-konsultasi.status', $jadwal) }}" class="px-4 pb-2 pt-1" @click.stop>
                                                @csrf
                                                @method('PATCH')
                                                <select name="status_slot" 
                                                    class="w-full text-xs font-semibold rounded-lg border-[#E2E8F0] py-1.5 pl-2.5 pr-6 focus:ring focus:ring-accent-blue/20 focus:border-accent-blue {{ $jadwal->status_slot === 'terisi' ? 'bg-gray-50 text-gray-400 cursor-not-allowed opacity-70' : 'bg-[#F8FAFC] text-navy-dark cursor-pointer hover:border-gray-300 transition' }}"
                                                    {{ $jadwal->status_slot === 'terisi' ? 'disabled' : '' }}
                                                    onchange="this.form.submit()">
                                                    @if($jadwal->status_slot === 'terisi')
                                                        <option value="terisi" selected>Terisi</option>
                                                    @else
                                                        <option value="tersedia" @selected($jadwal->status_slot === 'tersedia')>Tersedia</option>
                                                        <option value="tidak_aktif" @selected($jadwal->status_slot === 'tidak_aktif')>Tidak Aktif</option>
                                                    @endif
                                                </select>
                                            </form>
                                        </x-slot>
                                    </x-dropdown>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <x-empty-state title="Belum Ada Jadwal Konsultasi" message="Belum ada jadwal konsultasi." />
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
        </x-card>
    </div>
</x-app-layout>
