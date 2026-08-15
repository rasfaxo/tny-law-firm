<x-app-layout title="Jadwal Konsultasi" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Jadwal Konsultasi']]">

    <div class="space-y-6">
        <!-- Search and Filter Bar -->
        <x-card>
            <form method="GET" action="{{ route('klien.booking-konsultasi.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 max-w-md relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan judul perkara..." class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm pl-10 placeholder-gray-400 transition shadow-sm h-11">
                </div>
                
                <div class="flex items-center gap-3">
                    <select name="status_booking" onchange="this.form.submit()" class="bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4">
                        <option value="">Semua Status</option>
                        <option value="aktif" @selected(request('status_booking') === 'aktif')>Aktif</option>
                        <option value="selesai" @selected(request('status_booking') === 'selesai')>Selesai</option>
                        <option value="dibatalkan" @selected(request('status_booking') === 'dibatalkan')>Dibatalkan</option>
                    </select>
                    
                    @if(request('search') || request('status_booking'))
                        <a href="{{ route('klien.booking-konsultasi.index') }}" class="text-sm font-bold text-gray-500 hover:text-navy-dark transition px-2">
                            Reset
                        </a>
                    @endif
                    
                    <x-primary-button class="h-11">
                        Cari
                    </x-primary-button>
                </div>
            </form>
        </x-card>

        <!-- Bookings List Table -->
        <x-card class="p-0 overflow-hidden sm:p-0">
            <!-- Desktop Table Layout -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-[#F8FAFC]">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">No. Booking</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Perkara</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal & Waktu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse ($bookingKonsultasi as $booking)
                            @php
                                $jadwal = $booking->jadwalKonsultasi;
                                $perkara = $booking->praPendaftaranPerkara;
                            @endphp
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-accent-blue font-mono">
                                        BK-{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate">
                                    <span class="text-sm font-bold text-navy-dark block truncate">
                                        {{ $perkara->judul_perkara }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-600">
                                        {{ $perkara->kategori?->nama_kategori ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-navy-dark">
                                        {{ $jadwal?->tanggal?->translatedFormat('d M Y') ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400 font-medium">
                                        {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) . ' - ' . substr((string) $jadwal->waktu_selesai, 0, 5) : '-' }} WIB
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex text-xs font-bold px-2 py-0.5 rounded uppercase tracking-wider
                                        {{ $booking->metode_konsultasi === 'online' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                        {{ $booking->metode_konsultasi }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-status-badge :status="$booking->status_booking" />
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <a href="{{ route('klien.booking-konsultasi.show', $booking) }}" class="bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2 rounded-xl transition shadow-sm inline-flex items-center gap-1.5">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <x-empty-state title="Belum ada jadwal konsultasi" message="Anda belum memiliki jadwal konsultasi terdaftar saat ini." />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card Layout -->
            <div class="block md:hidden divide-y divide-gray-100 bg-white">
                @forelse ($bookingKonsultasi as $booking)
                    @php
                        $jadwal = $booking->jadwalKonsultasi;
                        $perkara = $booking->praPendaftaranPerkara;
                    @endphp
                    <div class="p-4 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-xs font-bold text-accent-blue font-mono">
                                BK-{{ str_pad($booking->id_booking, 3, '0', STR_PAD_LEFT) }}
                            </span>
                            <x-status-badge :status="$booking->status_booking" />
                        </div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">{{ $perkara->judul_perkara }}</h4>
                            <p class="text-xs text-gray-500 mt-1">Kategori: {{ $perkara->kategori?->nama_kategori ?? '-' }}</p>
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-gray-100">
                            <div class="text-xs text-gray-400 font-medium">
                                <div>{{ $jadwal?->tanggal?->translatedFormat('d M Y') ?? '-' }}</div>
                                <div class="text-[10px] text-gray-400 mt-0.5">
                                    {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) . ' - ' . substr((string) $jadwal->waktu_selesai, 0, 5) : '-' }} WIB
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="inline-flex text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wider
                                    {{ $booking->metode_konsultasi === 'online' ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ $booking->metode_konsultasi }}
                                </span>
                                <a href="{{ route('klien.booking-konsultasi.show', $booking) }}" class="bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-3 py-1.5 rounded-xl transition shadow-sm">
                                    Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <x-empty-state title="Belum ada jadwal konsultasi" message="Anda belum memiliki jadwal konsultasi terdaftar saat ini." />
                    </div>
                @endforelse
            </div>

            @if($bookingKonsultasi->hasPages())
                <div class="p-6 border-t border-[#F1F5F9] bg-[#F8FAFC]/50">
                    {{ $bookingKonsultasi->links() }}
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
