@props([
    'status',
    'color' => 'blue',
])

@php
    $colors = [
        'blue' => 'bg-blue-100 text-blue-800',
        'green' => 'bg-green-100 text-green-800',
        'orange' => 'bg-orange-100 text-orange-800',
        'yellow' => 'bg-yellow-100 text-yellow-800',
        'gray' => 'bg-gray-100 text-gray-800',
    ];

    $classes = $colors[$color] ?? $colors['blue'];
    $label = str_replace('_', ' ', ucfirst((string) $status));
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2 text-xs font-semibold leading-5 {$classes}"]) }}>
    {{ $label }}
</span>
