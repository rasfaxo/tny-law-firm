@php
    $officialLogoAvailable = is_file(public_path('brand/logo.svg'));
@endphp

@if ($officialLogoAvailable)
    <img src="{{ asset('brand/logo.svg') }}" alt="Logo {{ config('firm.name') }}" {{ $attributes }}>
@else
    <span role="img" aria-label="Identitas sementara {{ config('firm.name') }}" {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded-lg bg-navy-dark px-2.5 py-2 text-sm font-extrabold tracking-wider text-white']) }}>TNY</span>
@endif
