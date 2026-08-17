@php
    $roleName = match(Auth::user()->role) {
        'klien' => 'Klien',
        'admin' => 'Admin',
        'staf_legal' => 'Staf Legal',
        default => 'User'
    };
    $breadcrumbs = [
        ['label' => $roleName],
        ['label' => 'Profil Saya']
    ];
    if(request()->query('edit') === 'true') {
        $breadcrumbs[1]['url'] = route('profile.edit');
        $breadcrumbs[] = ['label' => 'Edit'];
    }
@endphp
<x-app-layout :title="request()->query('edit') === 'true' ? __('Edit Profil') : __('Profil Saya')" :breadcrumbs="$breadcrumbs">

    <div class="space-y-6">
        @if(request()->query('edit') !== 'true' && $user->role === 'klien')
            <div class="flex justify-end">
                <!-- Tombol Edit Profil -->
                <a href="{{ route('profile.edit', ['edit' => 'true']) }}" class="bg-navy-dark text-white hover:bg-navy-primary hover:shadow-md transition px-5 py-2.5 rounded-xl font-bold text-xs tracking-wider uppercase flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Edit Profil</span>
                </a>
            </div>
        @elseif(request()->query('edit') === 'true')
            <div class="flex justify-start">
                <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span>{{ __('Kembali') }}</span>
                </a>
            </div>
        @endif
        <!-- Status Notification -->
        @if (session('status') === 'profile-updated')
            <div class="rounded-xl bg-green-50 border border-green-200 p-4 flex gap-3 text-sm text-green-700">
                <svg class="h-5 w-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ __('Profil Anda berhasil diperbarui.') }}</span>
            </div>
        @endif

        @if(request()->query('edit') === 'true' && $user->role === 'klien')
            <!-- ================= FIGMA EDIT PROFILE FORM (node-id=65:1168) ================= -->
            <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-[#F1F5F9]">
                    <h4 class="font-bold text-navy-dark text-base">Edit Profil Klien</h4>
                    <p class="text-xs text-gray-400 mt-1">Perbarui data pendukung yang digunakan dalam proses pengajuan perkara.</p>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="p-6 space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nama Lengkap -->
                        <div class="space-y-1.5">
                            <x-input-label for="nama" :value="__('Nama Lengkap')" />
                            <x-text-input type="text" name="nama" id="nama" :value="old('nama', $user->nama)" required class="w-full text-navy-dark" />
                            <x-input-error class="mt-1" :messages="$errors->get('nama')" />
                        </div>

                        <!-- Email -->
                        <div class="space-y-1.5">
                            <x-input-label for="email" :value="__('Alamat Email')" />
                            <x-text-input type="email" name="email" id="email" :value="old('email', $user->email)" required class="w-full text-navy-dark" />
                            <x-input-error class="mt-1" :messages="$errors->get('email')" />
                        </div>

                        <!-- Nomor Telepon -->
                        <div class="space-y-1.5">
                            <x-input-label for="no_telepon" :value="__('Nomor Telepon')" />
                            <x-text-input type="text" name="no_telepon" id="no_telepon" :value="old('no_telepon', $user->no_telepon)" class="w-full text-navy-dark" />
                            <x-input-error class="mt-1" :messages="$errors->get('no_telepon')" />
                        </div>

                        <!-- Nomor Identitas (NIK) -->
                        <div class="space-y-1.5">
                            <x-input-label for="no_identitas" :value="__('NIK / No. Identitas')" />
                            <x-text-input type="text" name="no_identitas" id="no_identitas" :value="old('no_identitas', $user->profilKlien?->no_identitas)" class="w-full text-navy-dark" />
                            <x-input-error class="mt-1" :messages="$errors->get('no_identitas')" />
                        </div>

                        <!-- Pekerjaan -->
                        <div class="space-y-1.5">
                            <x-input-label for="pekerjaan" :value="__('Pekerjaan')" />
                            <x-text-input type="text" name="pekerjaan" id="pekerjaan" :value="old('pekerjaan', $user->profilKlien?->pekerjaan)" class="w-full text-navy-dark" />
                            <x-input-error class="mt-1" :messages="$errors->get('pekerjaan')" />
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="space-y-1.5">
                            <x-input-label for="jenis_kelamin" :value="__('Jenis Kelamin')" />
                            <x-select name="jenis_kelamin" id="jenis_kelamin" class="w-full text-navy-dark">
                                <option value="">Pilih Jenis Kelamin</option>
                                <option value="laki-laki" {{ old('jenis_kelamin', $user->profilKlien?->jenis_kelamin) === 'laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="perempuan" {{ old('jenis_kelamin', $user->profilKlien?->jenis_kelamin) === 'perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </x-select>
                            <x-input-error class="mt-1" :messages="$errors->get('jenis_kelamin')" />
                        </div>

                        <!-- Alamat -->
                        <div class="md:col-span-2 space-y-1.5">
                            <x-input-label for="alamat" :value="__('Alamat Lengkap')" />
                            <x-text-input tag="textarea" name="alamat" id="alamat" rows="3" class="w-full text-navy-dark">{{ old('alamat', $user->profilKlien?->alamat) }}</x-text-input>
                            <x-input-error class="mt-1" :messages="$errors->get('alamat')" />
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="border-t border-[#F1F5F9] pt-6 flex justify-end gap-3">
                        <x-secondary-button href="{{ route('profile.edit') }}" tag="a">
                            Batal
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            Simpan Perubahan
                        </x-primary-button>
                    </div>
                </form>
            </div>
        @else
            <!-- ================= FIGMA PROFILE DETAILS (node-id=65:793) ================= -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Data Akun Card (Left) -->
                <div class="lg:col-span-5 bg-white border border-[#E2E8F0] rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <div class="p-6 border-b border-[#F1F5F9]">
                        <h4 class="font-bold text-navy-dark text-base">Data Akun</h4>
                        <p class="text-xs text-gray-400 mt-1">Informasi dasar akun pengguna.</p>
                    </div>

                    <div class="p-6 space-y-5 flex-1">
                        <!-- Nama -->
                        <div class="border-b border-[#F1F5F9] pb-3 space-y-1">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Lengkap</span>
                            <span class="block text-sm font-semibold text-navy-dark">{{ $user->nama }}</span>
                        </div>

                        <!-- Email -->
                        <div class="border-b border-[#F1F5F9] pb-3 space-y-1">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Email</span>
                            <span class="block text-sm font-semibold text-navy-dark break-all">{{ $user->email }}</span>
                        </div>

                        <!-- Telepon -->
                        <div class="border-b border-[#F1F5F9] pb-3 space-y-1">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Telepon</span>
                            <span class="block text-sm font-semibold text-navy-dark">{{ $user->no_telepon ?? '-' }}</span>
                        </div>

                        <!-- Status Akun -->
                        <div class="space-y-2">
                            <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status Akun</span>
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 border border-green-200 uppercase tracking-wide">
                                {{ $user->status_akun }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Data Profil Pendukung Card (Right/Center) -->
                @if($user->role === 'klien')
                    <div class="lg:col-span-7 bg-white border border-[#E2E8F0] rounded-xl shadow-sm overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-[#F1F5F9]">
                            <h4 class="font-bold text-navy-dark text-base">Data Profil Pendukung</h4>
                            <p class="text-xs text-gray-400 mt-1">Kelengkapan data profil pendukung Klien.</p>
                        </div>

                        <div class="p-6 space-y-5 flex-1">
                            <!-- NIK -->
                            <div class="border-b border-[#F1F5F9] pb-3 space-y-1">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">NIK / No. Identitas</span>
                                <span class="block text-sm font-semibold text-navy-dark font-mono">{{ $user->profilKlien?->no_identitas ?? '-' }}</span>
                            </div>

                            <!-- Pekerjaan -->
                            <div class="border-b border-[#F1F5F9] pb-3 space-y-1">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Pekerjaan</span>
                                <span class="block text-sm font-semibold text-navy-dark">{{ $user->profilKlien?->pekerjaan ?? '-' }}</span>
                            </div>

                            <!-- Jenis Kelamin -->
                            <div class="border-b border-[#F1F5F9] pb-3 space-y-1">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis Kelamin</span>
                                <span class="block text-sm font-semibold text-navy-dark uppercase">{{ $user->profilKlien?->jenis_kelamin ?? '-' }}</span>
                            </div>

                            <!-- Alamat -->
                            <div class="space-y-1">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alamat Lengkap</span>
                                <span class="block text-sm font-semibold text-navy-dark leading-relaxed">{{ $user->profilKlien?->alamat ?? '-' }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Non-Klien Fallback form standard edit -->
                    <div class="lg:col-span-7 bg-white border border-[#E2E8F0] rounded-xl shadow-sm overflow-hidden p-6">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                @endif
            </div>
        @endif

        <!-- Card Update Password (Modular Breeze) -->
        <div class="bg-white border border-[#E2E8F0] rounded-xl shadow-sm overflow-hidden p-6 max-w-xl">
            @include('profile.partials.update-password-form')
        </div>
    </div>
</x-app-layout>
