<x-guest-layout>
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-navy-dark">Selamat Datang Kembali</h3>
        <p class="text-sm text-gray-500 mt-2">Silakan masuk menggunakan akun Anda untuk mengelola pra-pendaftaran perkara.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Alamat Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Kata Sandi')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mt-6">
            <!-- Kita tetap sediakan layout yang seimbang, tetapi tanpa remember_token jika tidak didukung database, di sini kita gunakan link Lupa Sandi -->
            <div>
                @if (Route::has('password.request'))
                    <a class="text-sm text-accent-blue hover:text-navy-dark transition duration-150" href="{{ route('password.request') }}">
                        {{ __('Lupa Kata Sandi?') }}
                    </a>
                @endif
            </div>

            <x-primary-button class="bg-[#1E3A8A] hover:bg-navy-dark text-white rounded-xl py-2 px-6">
                {{ __('Masuk') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 text-center border-t border-[#E2E8F0] pt-6">
        <p class="text-sm text-gray-500">
            Belum memiliki akun Klien? 
            <a href="{{ route('register') }}" class="text-accent-blue font-semibold hover:underline">Daftar Sekarang</a>
        </p>
    </div>
</x-guest-layout>
