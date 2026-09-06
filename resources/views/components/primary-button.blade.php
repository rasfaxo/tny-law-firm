@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'min-h-10 bg-navy-primary hover:bg-navy-dark text-white font-semibold text-sm px-4 py-2 rounded-lg transition shadow-sm inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed text-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-blue focus-visible:ring-offset-2']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'submit', 'class' => 'min-h-10 bg-navy-primary hover:bg-navy-dark text-white font-semibold text-sm px-4 py-2 rounded-lg transition shadow-sm inline-flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-blue focus-visible:ring-offset-2']) }}>
        {{ $slot }}
    </button>
@endif
