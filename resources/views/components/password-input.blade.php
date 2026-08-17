@props(['disabled' => false])

<div x-data="{ show: false }" class="relative w-full">
    <input 
        @disabled($disabled) 
        :type="show ? 'text' : 'password'"
        {{ $attributes->merge(['class' => 'w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4 pr-11 disabled:opacity-50 disabled:cursor-not-allowed']) }}
    >
    <button 
        type="button" 
        @click="show = !show"
        tabindex="-1"
        :aria-label="show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi'"
        class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-navy-dark focus:outline-none transition p-1 rounded-lg"
    >
        <!-- Eye Icon (Ketika Sandi Tersembunyi) -->
        <svg x-show="!show" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
        </svg>
        <!-- Eye-Slash Icon (Ketika Sandi Terbuka) -->
        <svg x-show="show" class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
        </svg>
    </button>
</div>
