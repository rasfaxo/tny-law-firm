<x-app-layout title="Detail Klien" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Data Klien', 'url' => route('admin.klien.index')], ['label' => 'Detail']]">

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <x-secondary-button href="{{ route('admin.klien.index') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>

            <x-primary-button href="{{ route('admin.klien.edit', $klien) }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>{{ __('Edit Data') }}</span>
            </x-primary-button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Info Dasar & Keamanan -->
            <div class="lg:col-span-1 space-y-6">
                @if (session('success'))
                    <x-alert-banner type="success">
                        {{ session('success') }}
                    </x-alert-banner>
                @endif

                <!-- Card Profil Dasar -->
                <x-card class="p-0 overflow-hidden">
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-4 border-b border-[#F1F5F9] pb-4">
                            <div class="h-12 w-12 rounded-full bg-blue-50 text-accent-blue border border-blue-100 flex items-center justify-center font-bold text-lg shrink-0">
                                {{ strtoupper(substr($klien->nama, 0, 1)) }}
                            </div>
                            <div>
                                <h3 class="font-bold text-navy-dark text-lg">{{ $klien->nama }}</h3>
                                <p class="text-xs text-gray-500 font-mono">{{ $klien->email }}</p>
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">No. Telepon</div>
                                <div class="mt-1 text-sm font-semibold text-navy-dark">{{ $klien->no_telepon ?? '-' }}</div>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Akun</div>
                                <div class="mt-1">
                                    @if($klien->status_akun === 'aktif')
                                        <span class="inline-flex rounded-full bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider text-emerald-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex rounded-full bg-rose-100 border border-rose-200 px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider text-rose-800">
                                            Nonaktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Terdaftar Sejak</div>
                                <div class="mt-1 text-sm font-semibold text-navy-dark">{{ $klien->created_at?->format('d M Y') ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-[#F1F5F9]">
                            <form method="POST" action="{{ route('admin.klien.status', $klien) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status_akun" value="{{ $klien->status_akun === 'aktif' ? 'nonaktif' : 'aktif' }}">
                                <button type="submit" class="w-full text-center px-4 py-2.5 text-sm font-bold rounded-xl transition border {{ $klien->status_akun === 'aktif' ? 'bg-rose-50 text-rose-700 border-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' }}">
                                    {{ $klien->status_akun === 'aktif' ? 'Blokir Akun Klien' : 'Aktifkan Akun Klien' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </x-card>

                <!-- Card Reset Password -->
                <x-card class="p-0 overflow-hidden" x-data="{ isSubmitting: false }">
                    <div class="p-6">
                        <h4 class="font-bold text-navy-dark text-sm mb-4">Reset Password Klien</h4>
                        <form method="POST" action="{{ route('admin.klien.password', $klien) }}" @submit="isSubmitting = true">
                            @csrf
                            @method('PATCH')
                            <div class="space-y-4">
                                <div>
                                    <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Password Baru</label>
                                    <input type="password" name="password" id="password" required minlength="8"
                                           class="mt-1 block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm">
                                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                                </div>
                                <button type="submit" class="w-full bg-navy-primary hover:bg-navy-dark text-white font-bold text-xs h-10 rounded-xl flex items-center justify-center transition shadow-md shadow-blue-900/20" x-bind:disabled="isSubmitting" x-bind:class="{ 'opacity-70 cursor-not-allowed': isSubmitting }">
                                    <span x-show="!isSubmitting">Update Password</span>
                                    <span x-show="isSubmitting" class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Menyimpan...
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </x-card>
            </div>

            <!-- Kolom Kanan: Riwayat Pengajuan -->
            <div class="lg:col-span-2">
                <x-card class="p-0 overflow-hidden">
                    <div class="p-6 border-b border-[#E2E8F0] flex justify-between items-center">
                        <h3 class="font-bold text-navy-dark text-lg">Riwayat Pengajuan Perkara</h3>
                        <span class="bg-[#F1F5F9] text-gray-500 font-bold text-xs px-3 py-1 rounded-lg">
                            Total: {{ $pengajuan->count() }}
                        </span>
                    </div>
                    
                    <div class="divide-y divide-[#F1F5F9]">
                        @forelse ($pengajuan as $item)
                            <div class="p-6 hover:bg-[#F8FAFC] transition duration-150">
                                <div class="flex justify-between items-start gap-4">
                                    <div>
                                        <div class="flex items-center gap-3 mb-1">
                                            <span class="text-xs font-bold font-mono text-navy-primary">
                                                PP-{{ str_pad($item->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <x-status-badge :status="$item->status_pengajuan" />
                                        </div>
                                        <h4 class="font-bold text-navy-dark text-sm">{{ $item->judul_perkara }}</h4>
                                        <p class="text-xs text-gray-500 mt-1"><span class="font-medium">Kategori:</span> {{ $item->kategori?->nama_kategori ?? '-' }}</p>
                                        <p class="text-xs text-gray-400 mt-0.5"><span class="font-medium">Diajukan:</span> {{ $item->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</p>
                                    </div>
                                    <div class="shrink-0">
                                        <a href="{{ route('admin.pra-pendaftaran.show', $item) }}" class="inline-flex items-center gap-1 text-xs font-bold text-accent-blue hover:underline bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100">
                                            <span>Lihat Detail</span>
                                            <svg class="h-3 w-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <x-empty-state title="Belum Ada Pengajuan" message="Klien ini belum pernah membuat pra-pendaftaran perkara apa pun di dalam sistem." />
                            </div>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>
