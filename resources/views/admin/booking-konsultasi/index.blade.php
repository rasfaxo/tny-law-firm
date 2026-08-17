<x-app-layout title="Booking Konsultasi" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Booking Konsultasi']]">

    <div class="space-y-6">
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

        <x-card class="p-0 sm:p-0">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-[#E2E8F0]">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Klien</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Perkara</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Jadwal</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Konfirmasi</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Booking</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-[#E2E8F0]">
                        @forelse ($bookingKonsultasi as $booking)
                            @php
                                $pengajuan = $booking->praPendaftaranPerkara;
                                $jadwal = $booking->jadwalKonsultasi;
                            @endphp
                            <tr class="hover:bg-[#F8FAFC] transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-navy-dark">
                                    {{ $booking->klien?->nama ?? '-' }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-navy-dark text-sm leading-snug">{{ $pengajuan?->judul_perkara ?? '-' }}</div>
                                    <div class="text-xs text-gray-400 font-semibold mt-0.5">{{ $pengajuan?->kategori?->nama_kategori ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-navy-dark text-sm">{{ $jadwal?->tanggal?->format('d M Y') ?? '-' }}</div>
                                    <div class="text-xs text-gray-500 font-mono mt-0.5">
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
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('admin.booking-konsultasi.show', $booking) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-accent-blue hover:underline transition">
                                        <span>Detail</span>
                                        <svg class="h-3.5 w-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <x-empty-state title="Belum Ada Booking Konsultasi" message="Belum ada data booking konsultasi yang tercatat." />
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
        </x-card>
    </div>
</x-app-layout>
