@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-center']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-rose-600 hover:bg-rose-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed']) }}>
        {{ $slot }}
    </button>
@endif
