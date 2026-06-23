<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Kelola Jadwal Konsultasi') }}
            </h2>

            <a href="{{ route('admin.jadwal-konsultasi.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Tambah Jadwal') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Admin Pembuat</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($jadwalKonsultasi as $jadwal)
                                    @php
                                        $statusColor = match ($jadwal->status_slot) {
                                            'tersedia' => 'green',
                                            'terisi' => 'blue',
                                            'tidak_aktif' => 'gray',
                                            default => 'blue',
                                        };
                                    @endphp
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $jadwal->tanggal?->format('d M Y') ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ substr((string) $jadwal->waktu_mulai, 0, 5) }} - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$jadwal->status_slot" :color="$statusColor" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            {{ $jadwal->admin?->nama ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex flex-wrap items-center justify-end gap-3">
                                                <a href="{{ route('admin.jadwal-konsultasi.show', $jadwal) }}" class="text-indigo-600 hover:text-indigo-900">Detail</a>

                                                @if ($jadwal->status_slot !== 'terisi')
                                                    <a href="{{ route('admin.jadwal-konsultasi.edit', $jadwal) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>

                                                    <form method="POST" action="{{ route('admin.jadwal-konsultasi.status', $jadwal) }}" class="flex items-center gap-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <select name="status_slot" class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="tersedia" @selected($jadwal->status_slot === 'tersedia')>{{ __('Tersedia') }}</option>
                                                            <option value="tidak_aktif" @selected($jadwal->status_slot === 'tidak_aktif')>{{ __('Tidak Aktif') }}</option>
                                                        </select>
                                                        <button type="submit" class="text-indigo-600 hover:text-indigo-900">
                                                            {{ __('Ubah') }}
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-gray-400">{{ __('Terkunci') }}</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada jadwal konsultasi.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $jadwalKonsultasi->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
