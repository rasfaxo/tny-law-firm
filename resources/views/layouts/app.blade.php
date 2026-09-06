<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow">

        <title>{{ config('app.name') }}</title>

        @if (is_file(public_path('brand/favicon.svg')))
            <link rel="icon" href="{{ asset('brand/favicon.svg') }}" type="image/svg+xml">
            <link rel="icon" href="{{ asset('brand/favicon-64.png') }}" type="image/png" sizes="64x64">
            <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
        @endif

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-navy-dark bg-background-light">
        <div class="h-screen flex overflow-hidden" x-data="sidebar">
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
                <main class="flex-1 overflow-y-auto p-4 sm:p-5 lg:p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
