<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TNY Law Firm') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-navy-dark bg-background-light">
        <div class="min-h-screen grid grid-cols-1 lg:grid-cols-12">
            <!-- Left Side: Navy Banner (Hidden on Mobile) -->
            <div class="hidden lg:flex lg:col-span-5 bg-navy-dark text-white p-12 flex-col justify-between relative overflow-hidden">
                <!-- Decorative subtle circle -->
                <div class="absolute bg-white/5 rounded-full w-[350px] h-[350px] -top-20 -left-20 blur-2xl"></div>
                <div class="absolute bg-accent-blue/10 rounded-full w-[250px] h-[250px] bottom-10 right-10 blur-3xl"></div>

                <div class="z-10">
                    <a href="/" class="inline-flex items-center gap-2">
                        <span class="font-extrabold text-xl tracking-wider text-white">TNY LAW FIRM</span>
                    </a>
                </div>

                <div class="space-y-6 z-10">
                    <h2 class="text-3xl font-extrabold leading-tight">Portal Pra-Pendaftaran Perkara & Konsultasi Hukum</h2>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                        Ajukan awal perkara hukum Anda secara efisien, kelola berkas secara digital, dan atur konsultasi dengan tim pengacara profesional kami secara terstruktur.
                    </p>
                </div>

                <div class="z-10 text-xs text-gray-500">
                    &copy; 2026 TNY Law Firm. All rights reserved.
                </div>
            </div>

            <!-- Right Side: Auth Form Content -->
            <div class="lg:col-span-7 flex flex-col justify-center px-6 py-12 md:px-12 bg-white">
                <div class="mx-auto w-full max-w-md">
                    <!-- Mobile Logo Branding (Visible only on Mobile) -->
                    <div class="flex flex-col items-center mb-8 lg:hidden">
                        <a href="/" class="font-extrabold text-2xl tracking-wider text-navy-dark">TNY LAW FIRM</a>
                        <span class="text-xs text-gray-500 mt-1">Portal Pra-Pendaftaran Perkara</span>
                    </div>

                    <!-- Session Status Alert -->
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
