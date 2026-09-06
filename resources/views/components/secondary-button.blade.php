@props(['href' => null])

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'min-h-10 bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-semibold text-sm px-4 py-2 rounded-lg transition shadow-sm inline-flex items-center justify-center gap-2 text-center focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-blue focus-visible:ring-offset-2']) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => 'button', 'class' => 'min-h-10 bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-semibold text-sm px-4 py-2 rounded-lg transition shadow-sm inline-flex items-center justify-center gap-2 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-blue focus-visible:ring-offset-2']) }}>
        {{ $slot }}
    </button>
@endif
