<x-app-layout title="Ajukan Reschedule" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Jadwal Konsultasi', 'url' => route('klien.booking-konsultasi.index')], ['label' => 'BK-' . str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT), 'url' => route('klien.booking-konsultasi.show', $bookingKonsultasi)], ['label' => 'Reschedule']]">

    @php
        $pengajuan = $bookingKonsultasi->praPendaftaranPerkara;
        $jadwal = $bookingKonsultasi->jadwalKonsultasi;
    @endphp

    <div class="space-y-6 max-w-5xl mx-auto" x-data="{ isSubmitting: false }">
        @if ($errors->any())
            <x-alert-banner type="error">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert-banner>
        @endif

        <!-- Alert Banner (Informasi Reschedule) -->
        <x-alert-banner type="info" title="Informasi Reschedule">
            Pengajuan reschedule akan menunggu persetujuan Admin. Jadwal lama tetap aktif hingga keputusan diberikan.
        </x-alert-banner>

        <form method="POST" action="{{ route('klien.permintaan-reschedule.store', $bookingKonsultasi) }}" class="space-y-6" @submit="isSubmitting = true">
            @csrf

            <!-- Card 1: Jadwal Saat Ini -->
            <x-card>
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Jadwal Saat Ini</h3>
                    <p class="text-sm text-gray-500 mt-1">Jadwal konsultasi yang saat ini terdaftar atas nama Anda.</p>
                </div>

                <x-divider />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">KODE BOOKING</span>
                        <span class="block text-sm font-bold text-navy-dark mt-1 font-mono">BK-{{ str_pad($bookingKonsultasi->id_booking, 3, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">METODE KONSULTASI</span>
                        <div class="mt-1">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $bookingKonsultasi->metode_konsultasi === 'online' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                                {{ ucfirst($bookingKonsultasi->metode_konsultasi) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">TANGGAL & WAKTU</span>
                        <span class="block text-sm font-bold text-navy-dark mt-1">
                            {{ $jadwal?->tanggal?->translatedFormat('l, d M Y') ?? '-' }} • {{ $jadwal ? substr((string) $jadwal->waktu_mulai, 0, 5) . '–' . substr((string) $jadwal->waktu_selesai, 0, 5) : '-' }} WIB
                        </span>
                    </div>

                    <div>
                        <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">STATUS BOOKING</span>
                        <div class="mt-1">
                            <x-status-badge :status="$bookingKonsultasi->status_booking" />
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Card 2: Alasan Reschedule -->
            <x-card class="space-y-6">
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Alasan Reschedule</h3>
                    <p class="text-sm text-gray-500 mt-1">Jelaskan alasan Anda mengajukan perubahan jadwal.</p>
                </div>
                
                <x-divider />

                <div>
                    <x-input-label for="alasan_reschedule" :value="__('Alasan Reschedule')" />
                    <x-text-input tag="textarea" id="alasan_reschedule" name="alasan_reschedule" rows="4" required class="resize-none" placeholder="Contoh: Terdapat kendala mendadak pada jadwal yang sudah ditentukan. Mohon penjadwalan ulang ke waktu yang lebih memungkinkan.">{{ old('alasan_reschedule') }}</x-text-input>
                    <x-input-error :messages="$errors->get('alasan_reschedule')" class="mt-2" />
                    <p class="mt-2 text-xs font-medium text-gray-400">* Wajib diisi. Alasan yang jelas membantu Admin memproses permintaan Anda lebih cepat.</p>
                </div>
            </x-card>

            <!-- Card 3: Pilih Jadwal Alternatif -->
            <x-card>
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Pilih Jadwal Alternatif</h3>
                    <p class="text-sm text-gray-500 mt-1">Pilih salah satu slot jadwal yang tersedia sebagai preferensi baru Anda.</p>
                </div>

                <x-divider />

                <div class="overflow-x-auto border border-[#E2E8F0] rounded-xl">
                    <table class="min-w-full divide-y divide-[#E2E8F0]">
                        <thead class="bg-[#F8FAFC]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-16"></th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Jam Mulai</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Jam Selesai</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Ketersediaan</th>
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
                                        <span class="inline-flex bg-green-50 text-green-700 font-bold text-xs px-3 py-1 rounded-full border border-green-200">Tersedia</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <x-empty-state title="Tidak Ada Jadwal" message="Belum ada slot jadwal alternatif yang tersedia saat ini." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <x-input-error :messages="$errors->get('preferensi_jadwal')" class="mt-2" />
            </x-card>

            <!-- Card 4: Preferensi Metode -->
            <x-card>
                <div>
                    <h3 class="font-bold text-navy-dark text-xl">Preferensi Metode</h3>
                    <p class="text-sm text-gray-500 mt-1">Preferensi metode konsultasi baru (opsional). Admin akan mempertimbangkan permintaan ini.</p>
                </div>

                <x-divider />

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
            </x-card>

            <!-- Action Buttons -->
            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-4 pt-4 pb-12">
                <x-secondary-button href="{{ route('klien.booking-konsultasi.show', $bookingKonsultasi) }}" class="w-full sm:w-auto">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Batal
                </x-secondary-button>
                
                <x-primary-button class="w-full sm:w-auto" ::disabled="isSubmitting">
                    <span x-show="!isSubmitting">Ajukan Reschedule</span>
                    <span x-show="isSubmitting" class="flex items-center justify-center gap-2" style="display: none;">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengirim...</span>
                    </span>
                </x-primary-button>
            </div>

        </form>
    </div>
</x-app-layout>
