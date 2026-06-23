<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Jadwal Konsultasi') }}
            </h2>

            @if ($jadwalKonsultasi->status_slot !== 'terisi')
                <a href="{{ route('admin.jadwal-konsultasi.edit', $jadwalKonsultasi) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ __('Edit') }}
                </a>
            @endif
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

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Tanggal</div>
                        <div class="mt-1">{{ $jadwalKonsultasi->tanggal?->format('d M Y') ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Waktu</div>
                        <div class="mt-1">
                            {{ substr((string) $jadwalKonsultasi->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwalKonsultasi->waktu_selesai, 0, 5) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Status Slot</div>
                        <div class="mt-1">
                            <x-status-badge :status="$jadwalKonsultasi->status_slot" :color="$statusColor" />
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Admin Pembuat</div>
                        <div class="mt-1">{{ $jadwalKonsultasi->admin?->nama ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Dibuat Pada</div>
                        <div class="mt-1">{{ $jadwalKonsultasi->created_at?->format('d M Y H:i') ?? '-' }}</div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4 pt-4">
                        <a href="{{ route('admin.jadwal-konsultasi.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali') }}
                        </a>

                        @if ($jadwalKonsultasi->status_slot !== 'terisi')
                            <form method="POST" action="{{ route('admin.jadwal-konsultasi.status', $jadwalKonsultasi) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status_slot" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="tersedia" @selected($jadwalKonsultasi->status_slot === 'tersedia')>{{ __('Tersedia') }}</option>
                                    <option value="tidak_aktif" @selected($jadwalKonsultasi->status_slot === 'tidak_aktif')>{{ __('Tidak Aktif') }}</option>
                                </select>
                                <x-primary-button>{{ __('Ubah Status') }}</x-primary-button>
                            </form>
                        @else
                            <div class="text-sm text-gray-500">
                                {{ __('Slot terisi tidak dapat diubah pada fase ini.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
