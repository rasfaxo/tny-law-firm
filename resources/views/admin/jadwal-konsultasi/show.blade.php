<x-app-layout title="Detail Jadwal Konsultasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Slot Jadwal', 'url' => route('admin.jadwal-konsultasi.index')], ['label' => 'Detail']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <x-secondary-button href="{{ route('admin.jadwal-konsultasi.index') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>
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

            @php
                $statusColor = match ($jadwalKonsultasi->status_slot) {
                    'tersedia' => 'green',
                    'terisi' => 'blue',
                    'tidak_aktif' => 'gray',
                    default => 'blue',
                };
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Info Card -->
                <x-card class="space-y-6">
                    <div>
                        <h3 class="font-bold text-navy-dark text-lg">Informasi Slot Jadwal</h3>
                        <p class="text-xs text-gray-400 mt-1">Detail ketersediaan dan status waktu konsultasi.</p>
                    </div>

                    <div class="space-y-4 divide-y divide-[#E2E8F0]">
                        <div class="pt-0 flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal</span>
                            <span class="text-sm font-semibold text-navy-dark">{{ $jadwalKonsultasi->tanggal?->format('d M Y') ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Waktu</span>
                            <span class="text-sm font-semibold text-navy-dark font-mono">
                                {{ substr((string) $jadwalKonsultasi->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalKonsultasi->waktu_selesai, 0, 5) }}
                            </span>
                        </div>

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Slot</span>
                            <x-status-badge :status="$jadwalKonsultasi->status_slot" />
                        </div>

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Admin Pembuat</span>
                            <span class="text-sm font-semibold text-navy-dark">{{ $jadwalKonsultasi->admin?->nama ?? '-' }}</span>
                        </div>

                        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-2 py-3">
                            <span class="text-xxs font-bold text-gray-400 uppercase tracking-wider">Dibuat Pada</span>
                            <span class="text-xs text-gray-500 font-semibold">{{ $jadwalKonsultasi->created_at?->format('d M Y H:i') ?? '-' }}</span>
                        </div>
                    </div>
                </x-card>

                <!-- Action Card -->
                <x-card class="space-y-6 flex flex-col justify-between">
                    <div class="space-y-4">
                        <div>
                            <h3 class="font-bold text-navy-dark text-lg">Aksi Slot</h3>
                            <p class="text-xs text-gray-400 mt-1">Ubah ketersediaan jadwal atau update informasinya.</p>
                        </div>

                        @if ($jadwalKonsultasi->status_slot !== 'terisi')
                            <form method="POST" action="{{ route('admin.jadwal-konsultasi.status', $jadwalKonsultasi) }}" class="space-y-4 pt-4 border-t border-[#E2E8F0]">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <x-input-label for="status_slot" :value="__('Ubah Ketersediaan')" />
                                    <select name="status_slot" class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4 mt-1">
                                        <option value="tersedia" @selected($jadwalKonsultasi->status_slot === 'tersedia')>{{ __('Tersedia') }}</option>
                                        <option value="tidak_aktif" @selected($jadwalKonsultasi->status_slot === 'tidak_aktif')>{{ __('Tidak Aktif') }}</option>
                                    </select>
                                </div>
                                <div class="flex justify-end pt-2">
                                    <x-primary-button>{{ __('Ubah Status') }}</x-primary-button>
                                </div>
                            </form>
                            <div class="pt-4 border-t border-[#E2E8F0] mt-4 flex justify-end">
                                <x-secondary-button href="{{ route('admin.jadwal-konsultasi.edit', $jadwalKonsultasi) }}" tag="a">
                                    {{ __('Edit Data Slot') }}
                                </x-secondary-button>
                            </div>
                        @else
                            <x-alert-banner type="warning">
                                {{ __('Slot terisi tidak dapat diubah pada fase ini.') }}
                            </x-alert-banner>
                        @endif
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
