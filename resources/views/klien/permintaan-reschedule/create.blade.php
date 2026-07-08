<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1">
            <span>Klien</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('klien.booking-konsultasi.index') }}" class="hover:underline">Jadwal Konsultasi</a>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('klien.booking-konsultasi.show', $bookingKonsultasi) }}" class="hover:underline text-gray-600 font-mono">BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }}</a>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Reschedule</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
            {{ __('Ajukan Reschedule') }}
        </h2>
    </x-slot>

    @php
        $pengajuan = $bookingKonsultasi->praPendaftaranPerkara;
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
    @endphp

    <div class="space-y-6">
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex gap-3 text-sm text-red-700 shadow-sm">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Alert Banner (Informasi Reschedule) -->
        <div class="bg-[#EFF6FF] border-l-4 border-accent-blue p-4 rounded-r-xl border border-y-blue-200 border-r-blue-200 shadow-sm">
            <div class="flex gap-2 items-center">
                <svg class="h-5 w-5 text-accent-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-bold text-blue-700 text-sm">Informasi Reschedule</span>
            </div>
            <p class="text-xs text-blue-700/80 mt-2 pl-7 leading-relaxed">
                Pengajuan reschedule akan menunggu persetujuan Admin. Jadwal konsultasi yang lama tetap aktif hingga keputusan disetujui Admin.
            </p>
        </div>

        <form method="POST" action="{{ route('klien.permintaan-reschedule.store', $bookingKonsultasi) }}" class="space-y-6">
            @csrf

            <!-- Card 1: Jadwal Saat Ini -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                    <h3 class="font-bold text-navy-dark text-lg">Jadwal Saat Ini</h3>
                    <p class="text-xs text-gray-500 mt-1">Jadwal konsultasi yang saat ini terdaftar atas nama Anda.</p>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="border-b md:border-b-0 pb-4 md:pb-0 border-gray-150">
                        <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Kode Booking</span>
                        <span class="block text-sm font-bold text-navy-dark mt-1 font-mono">BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="border-b md:border-b-0 pb-4 md:pb-0 border-gray-150">
                        <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal & Waktu</span>
                        <span class="block text-sm font-bold text-navy-dark mt-1">
                            {{ $jadwal?->tanggal?->translatedFormat('l, d M Y') ?? '-' }} • {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) . '–' . substr((string) $jadwal->waktu_selesai, 0, 5) : '-' }} WIB
                        </span>
                    </div>

                    <div class="border-b md:border-b-0 pb-4 md:pb-0 border-gray-150">
                        <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Metode Konsultasi</span>
                        <div class="mt-1">
                            <span class="inline-flex text-xxs font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-200">
                                {{ $bookingKonsultasi->metode_konsultasi }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Status Booking</span>
                        <div class="mt-1">
                            <x-status-badge :status="$bookingKonsultasi->status_booking" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Alasan Reschedule -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm p-6 sm:p-8 space-y-6">
                <div class="border-b border-[#F1F5F9] pb-4">
                    <h3 class="font-bold text-navy-dark text-lg">Alasan Reschedule</h3>
                    <p class="text-xs text-gray-500 mt-1">Jelaskan alasan Anda mengajukan perubahan jadwal.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="alasan_reschedule" :value="__('Alasan Reschedule')" class="!text-xs !font-bold !text-gray-600 !uppercase !tracking-wider mb-2" />
                        <textarea id="alasan_reschedule" name="alasan_reschedule" rows="4" required class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm resize-none" placeholder="Tuliskan kendala atau alasan mengajukan reschedule secara mendetail...">{{ old('alasan_reschedule') }}</textarea>
                        <x-input-error :messages="$errors->get('alasan_reschedule')" class="mt-2" />
                        <p class="mt-1.5 text-xxs text-gray-400">* Wajib diisi. Alasan yang jelas membantu Admin memproses permintaan Anda lebih cepat.</p>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <x-input-label for="preferensi_metode" :value="__('Preferensi Metode Baru')" class="!text-xs !font-bold !text-gray-600 !uppercase !tracking-wider mb-2" />
                            <select id="preferensi_metode" name="preferensi_metode" class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11">
                                <option value="">{{ __('Tetap gunakan metode lama') }}</option>
                                <option value="online" @selected(old('preferensi_metode') === 'online')>{{ __('Online') }}</option>
                                <option value="offline" @selected(old('preferensi_metode') === 'offline')>{{ __('Offline') }}</option>
                            </select>
                            <x-input-error :messages="$errors->get('preferensi_metode')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="preferensi_jadwal_display" :value="__('Preferensi Jadwal Baru (Terpilih)')" class="!text-xs !font-bold !text-gray-600 !uppercase !tracking-wider mb-2" />
                            <input type="text" id="preferensi_jadwal_display" readonly class="w-full bg-gray-50 border-[#E2E8F0] text-gray-500 rounded-xl text-sm h-11 focus:outline-none focus:ring-0" placeholder="Pilih salah satu slot di tabel bawah..." value="{{ old('preferensi_jadwal') }}">
                            <!-- Hidden field to submit preferensi_jadwal -->
                            <input type="hidden" id="preferensi_jadwal" name="preferensi_jadwal" value="{{ old('preferensi_jadwal') }}">
                            <x-input-error :messages="$errors->get('preferensi_jadwal')" class="mt-2" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Pilih Jadwal Alternatif -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50">
                    <h3 class="font-bold text-navy-dark text-lg">Pilih Jadwal Alternatif</h3>
                    <p class="text-xs text-gray-500 mt-1">Pilih salah satu slot jadwal yang tersedia sebagai preferensi baru Anda.</p>
                </div>
                
                <div class="p-6 sm:p-8">
                    <div class="overflow-x-auto border border-[#E2E8F0] rounded-xl">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#F8FAFC]">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider w-16">Pilih</th>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jam Mulai</th>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Jam Selesai</th>
                                    <th class="px-6 py-4 text-left text-xxs font-bold text-gray-400 uppercase tracking-wider">Ketersediaan</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-100">
                                @forelse ($jadwalKonsultasi as $slot)
                                    @php
                                        $labelJadwal = $slot->tanggal?->translatedFormat('l, d M Y') . ' • ' . substr((string) $slot->waktu_mulai, 0, 5) . '–' . substr((string) $slot->waktu_selesai, 0, 5) . ' WIB';
                                    @endphp
                                    <tr class="hover:bg-gray-50/40 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <input type="radio" name="reschedule_slot_radio" value="{{ $labelJadwal }}" onclick="selectSlot('{{ $labelJadwal }}')" class="border-gray-300 text-accent-blue shadow-sm focus:ring-accent-blue" @checked(old('preferensi_jadwal') === $labelJadwal)>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-navy-dark">
                                            {{ $slot->tanggal?->translatedFormat('l, d M Y') ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                            {{ substr((string) $slot->waktu_mulai, 0, 5) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                            {{ substr((string) $slot->waktu_selesai, 0, 5) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="bg-green-50 text-green-700 text-[10px] font-bold px-2 py-0.5 rounded-full border border-green-200 uppercase tracking-wider">Tersedia</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            Belum ada slot jadwal alternatif yang tersedia saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Form Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('klien.booking-konsultasi.show', $bookingKonsultasi) }}" class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm">
                    Batal
                </a>
                <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20">
                    Konfirmasi Pilihan
                </button>
            </div>
        </form>
    </div>

    <script>
        function selectSlot(text) {
            document.getElementById('preferensi_jadwal').value = text;
            document.getElementById('preferensi_jadwal_display').value = text;
        }
    </script>
</x-app-layout>
