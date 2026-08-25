@props([
    'title' => 'Belum ada data',
    'message' => 'Data yang Anda cari tidak ditemukan atau belum tersedia.',
])

<div {{ $attributes->merge(['class' => 'text-center py-12 px-4 border-2 border-dashed border-[#E2E8F0] rounded-xl bg-[#F8FAFC] flex flex-col items-center justify-center']) }}>
    <div class="h-12 w-12 rounded-full bg-white border border-[#E2E8F0] flex items-center justify-center shadow-sm mb-3">
        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
        </svg>
    </div>
    <h3 class="text-sm font-bold text-navy-dark">{{ $title }}</h3>
    <p class="mt-1 text-xs text-gray-500 max-w-sm mx-auto">{{ $message }}</p>
    
    @if(trim($slot) !== '')
        <div class="mt-5">
            {{ $slot }}
        </div>
    @endif
</div>
