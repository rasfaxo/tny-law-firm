<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-navy-dark bg-background-light">
        <div class="h-screen flex overflow-hidden" x-data="{ sidebarOpen: false }">
            <!-- Sidebar Navigation -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Reusable Top Header / Navbar -->
                <x-topbar :title="$title ?? null" :breadcrumbs="$breadcrumbs ?? null">
                    @isset($header)
                        <x-slot name="header">
                            {{ $header }}
                        </x-slot>
                    @endisset
                </x-topbar>

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto p-6 md:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
