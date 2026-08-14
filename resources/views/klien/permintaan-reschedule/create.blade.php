<x-app-layout title="Ajukan Reschedule" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Jadwal Konsultasi', 'url' => route('klien.booking-konsultasi.index')], ['label' => 'BK-' . str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT), 'url' => route('klien.booking-konsultasi.show', $bookingKonsultasi)], ['label' => 'Reschedule']]">

    @php
        $pengajuan = $bookingKonsultasi->praPendaftaranPerkara;
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
    @endphp

    <div class="space-y-6 max-w-5xl mx-auto" x-data="{ isSubmitting: false }">
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex gap-3 text-sm text-red-700 shadow-sm" x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); })">
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
        <div class="bg-white border border-blue-200 rounded-xl p-4 flex gap-3 text-sm text-blue-700 shadow-sm">
            <svg class="h-5 w-5 text-accent-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="space-y-1 mt-0.5">
                <p class="font-bold">Informasi Reschedule</p>
                <p class="text-[13px] text-blue-700/80">Pengajuan reschedule akan menunggu persetujuan Admin. Jadwal lama tetap aktif hingga keputusan diberikan.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('klien.permintaan-reschedule.store', $bookingKonsultasi) }}" class="space-y-6" @submit="isSubmitting = true">
            @csrf

            <!-- Card 1: Jadwal Saat Ini -->
            <div class="bg-white border border-[#E2E8F0] rounded-[20px] shadow-sm p-6 sm:p-8">
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Jadwal Saat Ini</h3>
                    <p class="text-sm text-gray-500 mt-1">Jadwal konsultasi yang saat ini terdaftar atas nama Anda.</p>
                </div>

                <hr class="border-[#F1F5F9] my-6">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">KODE BOOKING</span>
                        <span class="block text-sm font-bold text-navy-dark mt-1 font-mono">BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">METODE KONSULTASI</span>
                        <div class="mt-1">
                            <span class="inline-flex px-3 py-1 rounded-full text-[11px] font-bold border {{ $bookingKonsultasi->metode_konsultasi === 'online' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                {{ ucfirst($bookingKonsultasi->metode_konsultasi) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">TANGGAL & WAKTU</span>
                        <span class="block text-sm font-bold text-navy-dark mt-1">
                            {{ $jadwal?->tanggal?->translatedFormat('l, d M Y') ?? '-' }} • {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) . '–' . substr((string) $jadwal->waktu_selesai, 0, 5) : '-' }} WIB
                        </span>
                    </div>

                    <div>
                        <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">STATUS BOOKING</span>
                        <div class="mt-1">
                            <x-status-badge :status="$bookingKonsultasi->status_booking" />
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Alasan Reschedule -->
            <div class="bg-white border border-[#E2E8F0] rounded-[20px] shadow-sm p-6 sm:p-8">
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Alasan Reschedule</h3>
                    <p class="text-sm text-gray-500 mt-1">Jelaskan alasan Anda mengajukan perubahan jadwal.</p>
                </div>
                
                <hr class="border-[#F1F5F9] my-6">

                <div>
                    <x-input-label for="alasan_reschedule" :value="__('ALASAN RESCHEDULE')" class="!text-[11px] !font-bold !text-gray-400 !uppercase !tracking-wider mb-2" />
                    <textarea id="alasan_reschedule" name="alasan_reschedule" rows="4" required class="w-full bg-white border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm resize-none" placeholder="Contoh: Terdapat kendala mendadak pada jadwal yang sudah ditentukan. Mohon penjadwalan ulang ke waktu yang lebih memungkinkan.">{{ old('alasan_reschedule') }}</textarea>
                    <x-input-error :messages="$errors->get('alasan_reschedule')" class="mt-2" />
                    <p class="mt-2 text-[11px] font-medium text-gray-400">* Wajib diisi. Alasan yang jelas membantu Admin memproses permintaan Anda lebih cepat.</p>
                </div>
            </div>

            <!-- Card 3: Pilih Jadwal Alternatif -->
            <div class="bg-white border border-[#E2E8F0] rounded-[20px] shadow-sm p-6 sm:p-8">
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Pilih Jadwal Alternatif</h3>
                    <p class="text-sm text-gray-500 mt-1">Pilih salah satu slot jadwal yang tersedia sebagai preferensi baru Anda.</p>
                </div>

                <hr class="border-[#F1F5F9] my-6">

                <div class="overflow-x-auto border border-[#E2E8F0] rounded-xl">
                    <table class="min-w-full divide-y divide-[#E2E8F0]">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider w-16"></th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Jam Mulai</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Jam Selesai</th>
                                <th class="px-6 py-4 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider">Ketersediaan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#F1F5F9]">
                            @forelse ($jadwalKonsultasi as $slot)
                                @php
                                    $labelJadwal = $slot->tanggal?->translatedFormat('l, d M Y') . ' • ' . substr((string) $slot->waktu_mulai, 0, 5) . '–' . substr((string) $slot->waktu_selesai, 0, 5) . ' WIB';
                                @endphp
                                <tr class="hover:bg-gray-50/40 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <input type="radio" name="preferensi_jadwal" value="{{ $labelJadwal }}" class="border-gray-300 text-accent-blue shadow-sm focus:ring-accent-blue w-4 h-4 cursor-pointer" @checked(old('preferensi_jadwal') === $labelJadwal) required>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-navy-dark">
                                        {{ $slot->tanggal?->translatedFormat('l, d M Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        {{ substr((string) $slot->waktu_mulai, 0, 5) }} WIB
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                        {{ substr((string) $slot->waktu_selesai, 0, 5) }} WIB
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex bg-green-50 text-green-700 font-bold text-[11px] px-3 py-1 rounded-full border border-green-200">Tersedia</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <p class="text-sm font-semibold text-gray-500">Belum ada slot jadwal alternatif yang tersedia saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-input-error :messages="$errors->get('preferensi_jadwal')" class="mt-2" />
            </div>

            <!-- Card 4: Preferensi Metode -->
            <div class="bg-white border border-[#E2E8F0] rounded-[20px] shadow-sm p-6 sm:p-8">
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Preferensi Metode</h3>
                    <p class="text-sm text-gray-500 mt-1">Preferensi metode konsultasi baru (opsional). Admin akan mempertimbangkan permintaan ini.</p>
                </div>

                <hr class="border-[#F1F5F9] my-6">

                <div class="flex flex-col sm:flex-row gap-4">
                    <label class="cursor-pointer group relative flex items-center bg-white border border-[#E2E8F0] hover:border-accent-blue rounded-xl px-5 py-4 flex-1 transition shadow-sm has-[:checked]:border-accent-blue has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-accent-blue">
                        <input type="radio" name="preferensi_metode" value="online" class="peer border-gray-300 text-accent-blue shadow-sm focus:ring-accent-blue w-4 h-4 mr-4" @checked(old('preferensi_metode') === 'online')>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-navy-dark">Online</span>
                            <span class="text-xs text-gray-500">Melalui tautan virtual</span>
                        </div>
                    </label>

                    <label class="cursor-pointer group relative flex items-center bg-white border border-[#E2E8F0] hover:border-accent-blue rounded-xl px-5 py-4 flex-1 transition shadow-sm has-[:checked]:border-accent-blue has-[:checked]:bg-blue-50/50 has-[:checked]:ring-1 has-[:checked]:ring-accent-blue">
                        <input type="radio" name="preferensi_metode" value="offline" class="peer border-gray-300 text-accent-blue shadow-sm focus:ring-accent-blue w-4 h-4 mr-4" @checked(old('preferensi_metode') === 'offline')>
                        <div class="flex flex-col">
                            <span class="font-bold text-sm text-navy-dark">Offline</span>
                            <span class="text-xs text-gray-500">Hadir di kantor</span>
                        </div>
                    </label>
                </div>
                <x-input-error :messages="$errors->get('preferensi_metode')" class="mt-2" />
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 pb-12">
                <a href="{{ route('klien.booking-konsultasi.show', $bookingKonsultasi) }}" class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm inline-flex items-center justify-center gap-2 w-full sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Batal
                </a>
                
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto">
                    <span x-show="!isSubmitting">Ajukan Reschedule</span>
                    <span x-show="isSubmitting" class="flex items-center justify-center gap-2" style="display: none;">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengirim...</span>
                    </span>
                </button>
            </div>

        </form>
    </div>
</x-app-layout>
