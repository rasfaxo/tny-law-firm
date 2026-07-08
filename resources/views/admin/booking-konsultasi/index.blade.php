<x-app-layout title="Booking Konsultasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Booking Konsultasi']]">

    <div class="space-y-6">
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
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Klien</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Perkara</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jadwal</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Konfirmasi</th>
                            <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-4 text-right text-xxs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($bookingKonsultasi as $booking)
                            @php
                                $pengajuan = $booking->praPendaftaranPerkara;
                                $jadwal = $booking->jadwalKonsultasi;
                            @endphp
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-navy-dark">
                                    {{ $booking->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-navy-dark text-sm">{{ $pengajuan?->judul_perkara ?? '-' }}</div>
                                    <div class="text-xxs text-gray-400 font-semibold mt-0.5">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-navy-dark text-xs">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</div>
                                    <div class="text-xxs text-gray-400 font-mono mt-0.5">
                                        {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) : '-' }}
                                        @if ($jadwal)
                                            - {{ substr((string) $jadwal->waktu_selesai, 0, 5) }}
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$booking->metode_konsultasi" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$booking->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi'" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$booking->status_booking" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-bold">
                                    <a href="{{ route('admin.booking-konsultasi.show', $booking) }}" class="inline-flex items-center gap-1 text-accent-blue hover:underline transition">
                                        <span>Detail</span>
                                        <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-xs text-gray-400">
                                    Belum ada booking konsultasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($bookingKonsultasi->hasPages())
                <div class="px-6 py-4 border-t border-[#E2E8F0]">
                    {{ $bookingKonsultasi->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
