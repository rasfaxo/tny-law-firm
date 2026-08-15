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
                    <h3 class="font-bold text-navy-dark text-[16px]">Daftar Riwayat Verifikasi</h3>
                    <p class="text-[13px] text-gray-500 mt-1">Menampilkan pengajuan yang telah Anda verifikasi.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#F1F5F9]">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Tanggal Verifikasi</th>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Kode</th>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Klien</th>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Judul Perkara</th>
                                <th class="px-5 py-4 text-left text-[11px] font-bold text-gray-400 tracking-wider uppercase">Hasil Verifikasi</th>
                                <th class="px-5 py-4 text-right text-[11px] font-bold text-gray-400 tracking-wider uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#F1F5F9]">
                            @foreach ($riwayat as $item)
                                @php
                                    $pengajuan = $item->praPendaftaranPerkara;
                                @endphp
                                <tr class="hover:bg-gray-50/40 transition">
                                    <td class="px-5 py-4 whitespace-nowrap text-[13px] text-gray-500">
                                        {{ $item->tanggal_verifikasi?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-[13px] font-mono font-medium text-accent-blue">
                                        PP-{{ sprintf('%03d', $item->id_pendaftaran) }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-[13px] font-bold text-navy-dark">
                                        {{ $pengajuan?->klien?->nama ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-[13px] text-gray-600 font-medium">
                                        {{ $pengajuan?->judul_perkara ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $color = $item->status_verifikasi === 'berkas_lengkap' ? 'green' : 'red';
                                        @endphp
                                        <x-status-badge :status="$item->status_verifikasi" :color="$color" />
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-sm">
                                        @if ($pengajuan)
                                            <a href="{{ route('staf-legal.verifikasi-berkas.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-accent-blue hover:text-blue-800 transition font-bold">
                                                Detail
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="px-5 py-4 bg-white border-t border-[#F1F5F9]">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </x-card>
        @endif
    </div>
</x-app-layout>
