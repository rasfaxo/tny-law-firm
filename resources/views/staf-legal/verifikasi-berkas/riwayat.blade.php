<x-app-layout title="Riwayat Verifikasi" :breadcrumbs="[['label' => 'Staf Legal'], ['label' => 'Riwayat Verifikasi']]">

    <div class="space-y-6">
        @if (session('success'))
            <x-alert-banner type="success">
                {{ session('success') }}
            </x-alert-banner>
        @endif

        @if ($riwayat->isEmpty())
            <!-- Empty State -->
            <x-empty-state title="Riwayat Verifikasi" message="Halaman riwayat verifikasi berkas yang telah diproses akan ditampilkan di sini." class="py-24" />
        @else
            <!-- Riwayat Table -->
            <x-card class="p-0 overflow-hidden sm:p-0">
                <div class="px-6 py-4 border-b border-[#F1F5F9]">
                    <h3 class="font-bold text-navy-dark text-lg">Daftar Riwayat Verifikasi</h3>
                    <p class="text-xs text-gray-500 mt-1">Menampilkan pengajuan yang telah Anda verifikasi.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#E2E8F0]">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Tanggal Verifikasi</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Kode</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Klien</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Judul Perkara</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 tracking-wider uppercase">Hasil Verifikasi</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 tracking-wider uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#E2E8F0]">
                            @foreach ($riwayat as $item)
                                @php
                                    $pengajuan = $item->praPendaftaranPerkara;
                                @endphp
                                <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 font-mono">
                                        {{ $item->tanggal_verifikasi?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-bold text-accent-blue">
                                        PP-{{ str_pad($item->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-navy-dark">
                                        {{ $pengajuan?->klien?->nama ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                        {{ $pengajuan?->judul_perkara ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$item->status_verifikasi" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                        @if ($pengajuan)
                                            <a href="{{ route('staf-legal.verifikasi-berkas.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-navy-dark hover:text-accent-blue hover:underline transition">
                                                <span>Detail</span>
                                                <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-gray-400 font-normal">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($riwayat->hasPages())
                    <div class="px-6 py-4 bg-white border-t border-[#E2E8F0]">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </x-card>
        @endif
    </div>
</x-app-layout>
