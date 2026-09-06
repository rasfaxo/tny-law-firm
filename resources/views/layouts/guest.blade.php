<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ config('app.name', 'TNY Law Firm') }}</title>

        @if (is_file(public_path('brand/favicon.svg')))
            <link rel="icon" href="{{ asset('brand/favicon.svg') }}" type="image/svg+xml">
            <link rel="icon" href="{{ asset('brand/favicon-64.png') }}" type="image/png" sizes="64x64">
            <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-navy-dark bg-background-light">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
            <!-- Left Side: Navy Banner (Hidden on Mobile) -->
            <div class="hidden lg:flex lg:col-span-5 bg-navy-dark text-white p-10 flex-col justify-between relative overflow-hidden">
                <!-- Decorative subtle circle -->
                <div class="absolute bg-white/5 rounded-full w-[350px] h-[350px] -top-20 -left-20 blur-2xl"></div>
                <div class="absolute bg-accent-blue/10 rounded-full w-[250px] h-[250px] bottom-10 right-10 blur-3xl"></div>

                <div class="z-10">
                    <a href="/" class="inline-flex items-center gap-3">
                        <x-application-logo class="h-10 w-auto" />
                        <span class="font-extrabold text-lg tracking-wider text-white">{{ config('firm.name') }}</span>
                    </a>
                </div>

                <div class="space-y-6 z-10">
                    <h2 class="text-3xl font-extrabold leading-tight">Portal Pra-Pendaftaran Perkara & Konsultasi Hukum</h2>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                        Ajukan awal perkara hukum Anda secara efisien, kelola berkas secara digital, dan atur konsultasi dengan tim pengacara profesional kami secara terstruktur.
                    </p>
                </div>

                <div class="z-10 text-xs text-gray-500">
                    &copy; {{ now()->year }} {{ config('firm.name') }}. Hak cipta dilindungi.
                </div>
            </div>

            <!-- Right Side: Auth Form Content -->
            <div class="lg:col-span-7 flex flex-col justify-center px-5 py-8 md:px-10 bg-white">
                <div class="mx-auto w-full max-w-md">
                    <!-- Mobile Logo Branding (Visible only on Mobile) -->
                    <div class="flex flex-col items-center mb-6 lg:hidden">
                        <a href="/" class="flex items-center gap-2 font-extrabold text-lg tracking-wider text-navy-dark">
                            <x-application-logo class="h-10 w-auto" />
                            {{ config('firm.name') }}
                        </a>
                        <span class="text-xs text-gray-500 mt-1">Portal Pra-Pendaftaran Perkara</span>
                    </div>

                    <!-- Session Status Alert -->
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
