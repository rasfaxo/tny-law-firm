<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('code') - @yield('title') | {{ config('firm.name') }}</title>
    @if (is_file(public_path('brand/favicon.svg')))
        <link rel="icon" href="{{ asset('brand/favicon.svg') }}" type="image/svg+xml">
        <link rel="icon" href="{{ asset('brand/favicon-64.png') }}" type="image/png" sizes="64x64">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-background-light font-sans text-navy-dark antialiased">
    <main class="mx-auto flex min-h-screen max-w-xl items-center px-5 py-10">
        <section class="w-full rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm sm:p-8" aria-labelledby="error-title">
            <x-application-logo class="mx-auto h-12 w-auto" />
            <p class="mt-6 text-sm font-bold uppercase tracking-[0.2em] text-accent-blue">Error @yield('code')</p>
            <h1 id="error-title" class="mt-2 text-2xl font-extrabold">@yield('title')</h1>
            <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-slate-600">@yield('message')</p>
            <a href="{{ url('/') }}" class="mt-6 inline-flex min-h-10 items-center justify-center rounded-lg bg-navy-primary px-4 py-2 text-sm font-semibold text-white hover:bg-navy-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-accent-blue focus-visible:ring-offset-2">
                Kembali ke beranda
            </a>
        </section>
    </main>
</body>
</html>
