<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1">
            <span>Admin</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Staf Legal</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span class="text-gray-600">Tambah</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
            {{ __('Tambah Akun Staf Legal') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm">
            <form method="POST" action="{{ route('admin.staf-legal.store') }}" class="space-y-6">
                @csrf

                <!-- Nama -->
                <div>
                    <label for="nama" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <input id="nama" name="nama" type="text" 
                        class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4" 
                        value="{{ old('nama') }}" required autofocus placeholder="Masukkan nama lengkap staf legal" />
                    <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Alamat Email</label>
                    <input id="email" name="email" type="email" 
                        class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4" 
                        value="{{ old('email') }}" required placeholder="Contoh: staf.legal@tny.co.id" />
                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                </div>

                <!-- No. Telepon -->
                <div>
                    <label for="no_telepon" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Nomor Telepon</label>
                    <input id="no_telepon" name="no_telepon" type="text" 
                        class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4" 
                        value="{{ old('no_telepon') }}" placeholder="Masukkan nomor telepon aktif" />
                    <x-input-error class="mt-2" :messages="$errors->get('no_telepon')" />
                </div>

                <!-- Status Akun -->
                <div>
                    <label for="status_akun" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Status Akun</label>
                    <select id="status_akun" name="status_akun" 
                        class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm h-11 px-4" required>
                        <option value="aktif" @selected(old('status_akun', 'aktif') === 'aktif')>Aktif</option>
                        <option value="nonaktif" @selected(old('status_akun') === 'nonaktif')>Nonaktif</option>
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('status_akun')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Password</label>
                        <input id="password" name="password" type="password" 
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4" 
                            required placeholder="Minimal 8 karakter" />
                        <x-input-error class="mt-2" :messages="$errors->get('password')" />
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Konfirmasi Password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" 
                            class="w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4" 
                            required placeholder="Ulangi password" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#E2E8F0]">
                    <a href="{{ route('admin.staf-legal.index') }}" 
                        class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-6 py-2.5 rounded-xl transition shadow-sm">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" 
                        class="inline-flex items-center justify-center bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-xs px-6 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 uppercase tracking-widest gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                        </svg>
                        <span>{{ __('Simpan') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
