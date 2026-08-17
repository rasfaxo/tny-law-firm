@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'bg-navy-primary hover:bg-navy-dark text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-center']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-navy-primary hover:bg-navy-dark text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
        {{ $slot }}
    </button>
@endif
