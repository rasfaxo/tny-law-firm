@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-center']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
        {{ $slot }}
    </button>
@endif
