<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-[4px] text-sm text-[#94a3b8]">
            <span>Staf Legal</span>
            <svg class="h-[12px] w-[12px] text-[#94a3b8]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-[#475569] font-medium">Riwayat Verifikasi</span>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-700 shadow-sm border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($riwayat->isEmpty())
            <!-- Empty State from Figma screen 90-1266 -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] py-[100px] flex flex-col items-center justify-center text-center px-4">
                <div class="bg-[#fffbeb] text-amber-500 w-16 h-16 rounded-[20px] flex items-center justify-center shadow-sm">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="font-bold text-[18px] text-[#0f172a] mt-6">Riwayat Verifikasi</h3>
                <p class="text-[14px] text-[#64748b] max-w-[384px] mt-2">
                    Halaman riwayat verifikasi berkas yang telah diproses akan ditampilkan di sini.
                </p>
            </div>
        @else
            <!-- Riwayat Table -->
            <div class="bg-white border border-[#e2e8f0] rounded-[16px] shadow-[0px_1px_3px_rgba(15,23,42,0.06),0px_8px_24px_rgba(15,23,42,0.04)] overflow-hidden">
                <div class="px-[20px] py-[16px] border-b border-[#f1f5f9]">
                    <h3 class="font-bold text-[14px] text-[#0f172a]">Daftar Riwayat Verifikasi</h3>
                    <p class="text-[12px] text-[#94a3b8] mt-0.5">Menampilkan pengajuan yang telah Anda verifikasi.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-[#f8fafc]">
                            <tr>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Tanggal Verifikasi</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Kode</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Klien</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Judul Perkara</th>
                                <th class="px-5 py-4 text-left text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Hasil Verifikasi</th>
                                <th class="px-5 py-4 text-right text-[11px] font-semibold text-[#64748b] tracking-wider uppercase">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#f1f5f9]">
                            @foreach ($riwayat as $item)
                                @php
                                    $pengajuan = $item->praPendaftaranPerkara;
                                @endphp
                                <tr>
                                    <td class="px-5 py-4 whitespace-nowrap text-[13px] text-[#64748b]">
                                        {{ $item->tanggal_verifikasi?->format('d M Y H:i') ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-[13px] font-mono font-medium text-[#1e3a8a]">
                                        PP-{{ sprintf('%03d', $item->id_pendaftaran) }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-[13px] font-medium text-[#334155]">
                                        {{ $pengajuan?->klien?->nama ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-[13px] text-[#334155]">
                                        {{ $pengajuan?->judul_perkara ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        @php
                                            $color = $item->status_verifikasi === 'berkas_lengkap' ? 'green' : 'red';
                                        @endphp
                                        <x-status-badge :status="$item->status_verifikasi" :color="$color" />
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right text-[12px] font-semibold">
                                        @if ($pengajuan)
                                            <a href="{{ route('staf-legal.verifikasi-berkas.show', $pengajuan) }}" class="inline-flex items-center gap-1 text-[#1d4ed8] hover:text-[#1e40af] transition duration-150">
                                                Detail
                                                <svg class="h-[11px] w-[11px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                </svg>
                                            </a>
                                        @else
                                            <span class="text-[#94a3b8] font-normal">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($riwayat->hasPages())
                    <div class="px-5 py-4 bg-white border-t border-[#f1f5f9]">
                        {{ $riwayat->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
