<x-guest-layout>
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-navy-dark">Daftar Akun Klien Baru</h3>
        <p class="text-sm text-gray-500 mt-2">Silakan isi formulir di bawah ini untuk membuat akun Klien Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nama Lengkap -->
        <div>
            <x-input-label for="nama" :value="__('Nama Lengkap')" />
            <x-text-input id="nama" class="block mt-1 w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <!-- Alamat Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Nomor Telepon -->
        <div class="mt-4">
            <x-input-label for="no_telepon" :value="__('Nomor Telepon')" />
            <x-text-input id="no_telepon" class="block mt-1 w-full" type="text" name="no_telepon" :value="old('no_telepon')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('no_telepon')" class="mt-2" />
        </div>

        <!-- Kata Sandi -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-password-input id="password" class="block mt-1 w-full"
                             name="password"
                             required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Kata Sandi')" />
            <x-password-input id="password_confirmation" class="block mt-1 w-full"
                             name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-8">
            <x-primary-button class="py-2 px-6 w-full justify-center">
                {{ __('Daftar Akun') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 text-center border-t border-[#E2E8F0] pt-6">
        <p class="text-sm text-gray-500">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="text-accent-blue font-semibold hover:underline">Masuk Akun</a>
        </p>
    </div>
</x-guest-layout>
