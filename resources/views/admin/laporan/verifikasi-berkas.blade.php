<x-app-layout title="Laporan Verifikasi Berkas" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Laporan', 'url' => route('admin.laporan.index')], ['label' => 'Verifikasi Berkas']]">

    <style>
        @media print {
            nav, header, .no-print { display: none !important; }
            body { background: #fff !important; }
            .print-area { box-shadow: none !important; border: none !important; }
        }
    </style>

    @php
        $statusOptions = [
            'berkas_lengkap' => 'Berkas Lengkap',
            'berkas_tidak_lengkap' => 'Berkas Tidak Lengkap',
        ];
    @endphp

    <div class="space-y-6">
        <div class="max-w-full mx-auto space-y-6">
            <x-card class="no-print p-0 overflow-hidden sm:p-0">
                <div class="p-6 text-gray-900">
                    <form method="GET" action="{{ route('admin.laporan.verifikasi-berkas') }}" class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="tanggal_mulai" class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                            <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $filters['tanggal_mulai'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="tanggal_selesai" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                            <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $filters['tanggal_selesai'] ?? '' }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="status_verifikasi" class="block text-sm font-medium text-gray-700">Status Verifikasi</label>
                            <select id="status_verifikasi" name="status_verifikasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                @foreach ($statusOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($filters['status_verifikasi'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="id_staf_legal" class="block text-sm font-medium text-gray-700">Staf Legal</label>
                            <select id="id_staf_legal" name="id_staf_legal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Staf Legal</option>
                                @foreach ($stafLegal as $staf)
                                    <option value="{{ $staf->id_user }}" @selected((string) ($filters['id_staf_legal'] ?? '') === (string) $staf->id_user)>{{ $staf->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2 lg:col-span-4 flex flex-wrap items-center gap-3 pt-4 border-t border-[#E2E8F0]">
                            <x-primary-button>
                                {{ __('Terapkan Filter') }}
                            </x-primary-button>
                            <x-secondary-button href="{{ route('admin.laporan.verifikasi-berkas') }}" tag="a">
                                {{ __('Reset Filter') }}
                            </x-secondary-button>
                            <x-primary-button type="button" onclick="window.print()" class="bg-emerald-600 hover:bg-emerald-700 shadow-emerald-900/20">
                                {{ __('Cetak') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-card>

            <x-card class="print-area p-0 overflow-hidden sm:p-0">
                <div class="p-6 text-gray-900">
                    <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Laporan Verifikasi Berkas') }}</h3>
                            <p class="text-sm text-gray-600">{{ __('Jumlah data:') }} {{ $laporan->count() }}</p>
                        </div>
                        <div class="text-sm text-gray-600">
                            @if (($filters['tanggal_mulai'] ?? null) || ($filters['tanggal_selesai'] ?? null))
                                Periode: {{ $filters['tanggal_mulai'] ?? 'awal' }} s/d {{ $filters['tanggal_selesai'] ?? 'akhir' }}
                            @else
                                Periode: Semua data
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">ID Verifikasi</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nama Klien</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Judul Perkara</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Kategori Perkara</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Nama Staf Legal</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Status Verifikasi</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Verifikasi</th>
                                    <th class="px-3 py-3 text-left font-medium text-gray-500 uppercase tracking-wider">Catatan Umum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($laporan as $verifikasi)
                                    @php
                                        $pengajuan = $verifikasi->praPendaftaranPerkara;
                                        $statusColor = $verifikasi->status_verifikasi === 'berkas_lengkap' ? 'green' : 'red';
                                    @endphp
                                    <tr>
                                        <td class="px-3 py-3 text-gray-700">{{ $loop->iteration }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $verifikasi->id_verifikasi }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan?->klien?->nama ?? '-' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ $pengajuan?->judul_perkara ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $verifikasi->stafLegal?->nama ?? '-' }}</td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$verifikasi->status_verifikasi" :color="$statusColor" />
                                        </td>
                                        <td class="px-3 py-3 whitespace-nowrap text-gray-700">{{ $verifikasi->tanggal_verifikasi?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-3 py-3 text-gray-700">{{ \Illuminate\Support\Str::limit($verifikasi->catatan_umum ?? '-', 120) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-3 py-12 text-center">
                                            <x-empty-state title="Tidak Ada Data" message="Tidak ada data verifikasi sesuai filter." />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
