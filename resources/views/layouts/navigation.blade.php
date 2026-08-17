@php
    $user = Auth::user();
    
    // Penentuan Dashboard Route berdasarkan role
    $dashboardRoute = match ($user->role) {
        'klien' => 'klien.dashboard',
        'admin' => 'admin.dashboard',
        'staf_legal' => 'staf-legal.dashboard',
        default => 'dashboard',
    };

    // Penentuan Subtitle Portal berdasarkan role
    $portalSubtitle = match ($user->role) {
        'klien' => 'Portal Klien',
        'admin' => 'Portal Admin',
        'staf_legal' => 'Portal Staf Legal',
        default => 'Portal Layanan',
    };

    // Generate inisial nama
    $initials = collect(explode(' ', $user->nama))
        ->map(fn($n) => $n[0] ?? '')
        ->take(2)
        ->join('');
@endphp

<!-- Desktop Sidebar -->
<aside class="w-[264px] bg-[#0b1830] text-gray-300 h-screen flex flex-col justify-between shrink-0 hidden lg:flex border-r border-[#1a2e4a]">
    <div class="flex flex-col">
        <!-- Sidebar Branding (Logo) -->
        <div class="flex items-center gap-2.5 pb-5 pt-7 px-6">
            <!-- Gold Accent Stripe -->
            <div class="bg-[#d4af37] h-7 w-2 rounded shrink-0"></div>
            <div class="flex flex-col">
                <span class="font-bold text-base leading-tight text-white tracking-tight">TNY Law Firm</span>
                <span class="font-medium text-[#7c9cc5] text-xs uppercase tracking-wider mt-0.5">{{ $portalSubtitle }}</span>
            </div>
        </div>

        <!-- Divider -->
        <div class="px-6 pb-3">
            <div class="bg-[#1a2e4a] h-px w-full"></div>
        </div>

        <!-- Navigation Links -->
        <nav class="px-3 space-y-1 mt-2">
            @if ($user->isKlien())
                <!-- Dashboard Klien -->
                <a href="{{ route('klien.dashboard') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('klien.dashboard') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('klien.dashboard'))
                        <!-- Active Gold Indicator -->
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Profil Saya -->
                <a href="{{ route('profile.edit') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('profile.edit') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('profile.edit'))
                        <!-- Active Gold Indicator -->
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profil Saya</span>
                </a>

                <!-- Pengajuan -->
                <a href="{{ route('klien.pra-pendaftaran.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('klien.pra-pendaftaran.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('klien.pra-pendaftaran.*'))
                        <!-- Active Gold Indicator -->
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Pengajuan</span>
                </a>

                <!-- Jadwal Konsultasi -->
                <a href="{{ route('klien.booking-konsultasi.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('klien.booking-konsultasi.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('klien.booking-konsultasi.*'))
                        <!-- Active Gold Indicator -->
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Jadwal Konsultasi</span>
                </a>

            @elseif ($user->isAdmin())
                <!-- Dashboard Admin -->
                <a href="{{ route('admin.dashboard') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.dashboard') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.dashboard'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Kelola Staf Legal -->
                <a href="{{ route('admin.staf-legal.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.staf-legal.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.staf-legal.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Kelola Staf Legal</span>
                </a>

                <!-- Data Klien -->
                <a href="{{ route('admin.klien.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.klien.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.klien.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Data Klien</span>
                </a>

                <!-- Kategori Perkara -->
                <a href="{{ route('admin.kategori-perkara.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.kategori-perkara.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.kategori-perkara.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    <span>Kategori Perkara</span>
                </a>

                <!-- Data Pengajuan -->
                <a href="{{ route('admin.pra-pendaftaran.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.pra-pendaftaran.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.pra-pendaftaran.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Data Pengajuan</span>
                </a>

                <!-- Slot Jadwal -->
                <a href="{{ route('admin.jadwal-konsultasi.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.jadwal-konsultasi.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.jadwal-konsultasi.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Slot Jadwal</span>
                </a>

                <!-- Booking Konsultasi -->
                <a href="{{ route('admin.booking-konsultasi.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.booking-konsultasi.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.booking-konsultasi.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>Booking Konsultasi</span>
                </a>

                <!-- Permintaan Reschedule -->
                <a href="{{ route('admin.permintaan-reschedule.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.permintaan-reschedule.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.permintaan-reschedule.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Permintaan Reschedule</span>
                </a>

                <!-- Laporan Rekap -->
                <a href="{{ route('admin.laporan.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('admin.laporan.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('admin.laporan.*'))
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Laporan Rekap</span>
                </a>

            @elseif ($user->isStafLegal())
                <!-- Dashboard Staf Legal -->
                <a href="{{ route('staf-legal.dashboard') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('staf-legal.dashboard') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('staf-legal.dashboard'))
                        <!-- Active Gold Indicator -->
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- Pengajuan Verifikasi -->
                <a href="{{ route('staf-legal.verifikasi-berkas.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ (request()->routeIs('staf-legal.verifikasi-berkas.*') && !request()->routeIs('staf-legal.verifikasi-berkas.riwayat')) ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('staf-legal.verifikasi-berkas.*') && !request()->routeIs('staf-legal.verifikasi-berkas.riwayat'))
                        <!-- Active Gold Indicator -->
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <span>Pengajuan Verifikasi</span>
                </a>

                <!-- Riwayat Verifikasi -->
                <a href="{{ route('staf-legal.verifikasi-berkas.riwayat') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition duration-150 {{ request()->routeIs('staf-legal.verifikasi-berkas.riwayat') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    @if(request()->routeIs('staf-legal.verifikasi-berkas.riwayat'))
                        <!-- Active Gold Indicator -->
                        <div class="absolute bg-[#d4af37] h-6 left-0 rounded-r top-2.5 w-1"></div>
                    @endif
                    <svg class="h-4.5 w-4.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Riwayat Verifikasi</span>
                </a>
            @endif
        </nav>
    </div>

    <!-- Sidebar Profile Box (Figma node-id=65:854) -->
    <div class="pb-5 px-3">
        <div class="bg-[#132240] rounded-xl px-4 py-3 flex gap-3 items-center">
            <!-- Initials Avatar -->
            <div class="bg-[#1e3a8a] border border-[#2d5099] rounded-full w-9 h-9 shrink-0 flex items-center justify-center text-white font-bold text-xs">
                {{ $initials }}
            </div>
            <!-- Name & Role -->
            <div class="flex flex-col min-w-0">
                <span class="font-semibold text-white text-sm truncate leading-tight">{{ $user->nama }}</span>
                <span class="text-[#7c9cc5] text-xs mt-0.5 leading-none capitalize">
                    {{ str_replace('_', ' ', $user->role) }}
                </span>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Drawer (Overlay & Panel) -->
<div class="fixed inset-0 z-40 lg:hidden" 
     x-show="sidebarOpen" 
     x-transition:enter="transition-opacity ease-linear duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-linear duration-300"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0" 
     style="display: none;">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm" @click="sidebarOpen = false"></div>

    <!-- Panel Drawer -->
    <div class="fixed inset-y-0 left-0 flex flex-col w-[264px] bg-[#0b1830] text-gray-300 border-r border-[#1a2e4a] transform transition-transform ease-in-out duration-300"
         x-show="sidebarOpen"
         x-transition:enter="transition-transform ease-in-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition-transform ease-in-out duration-300"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         @click.away="sidebarOpen = false">
         
        <!-- Drawer Header -->
        <div class="flex items-center justify-between pb-5 pt-7 px-6 border-b border-[#1a2e4a]">
            <div class="flex items-center gap-2.5">
                <div class="bg-[#d4af37] h-6 w-1.5 rounded shrink-0"></div>
                <div class="flex flex-col">
                    <span class="font-bold text-base leading-tight text-white tracking-tight">TNY Law Firm</span>
                    <span class="font-medium text-[#7c9cc5] text-xs uppercase tracking-wider mt-0.5">{{ $portalSubtitle }}</span>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white focus:outline-none shrink-0">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Drawer Navigation links -->
        <nav class="px-3 space-y-1 mt-4 flex-1 overflow-y-auto">
            @if ($user->isKlien())
                <a href="{{ route('klien.dashboard') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('klien.dashboard') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('profile.edit') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('profile.edit') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    <span>Profil Saya</span>
                </a>
                <a href="{{ route('klien.pra-pendaftaran.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('klien.pra-pendaftaran.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    <span>Pengajuan</span>
                </a>
                <a href="{{ route('klien.booking-konsultasi.index') }}" 
                   class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('klien.booking-konsultasi.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240] hover:text-white' }}">
                    <span>Jadwal Konsultasi</span>
                </a>
            @elseif ($user->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.staf-legal.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.staf-legal.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Kelola Staf Legal</span>
                </a>
                <a href="{{ route('admin.klien.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.klien.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Data Klien</span>
                </a>
                <a href="{{ route('admin.kategori-perkara.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.kategori-perkara.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Kategori Perkara</span>
                </a>
                <a href="{{ route('admin.pra-pendaftaran.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.pra-pendaftaran.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Data Pengajuan</span>
                </a>
                <a href="{{ route('admin.jadwal-konsultasi.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.jadwal-konsultasi.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Slot Jadwal</span>
                </a>
                <a href="{{ route('admin.booking-konsultasi.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.booking-konsultasi.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Booking Konsultasi</span>
                </a>
                <a href="{{ route('admin.permintaan-reschedule.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.permintaan-reschedule.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Permintaan Reschedule</span>
                </a>
                <a href="{{ route('admin.laporan.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('admin.laporan.*') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Laporan Rekap</span>
                </a>
            @elseif ($user->isStafLegal())
                <a href="{{ route('staf-legal.dashboard') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('staf-legal.dashboard') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('staf-legal.verifikasi-berkas.index') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ (request()->routeIs('staf-legal.verifikasi-berkas.*') && !request()->routeIs('staf-legal.verifikasi-berkas.riwayat')) ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Pengajuan Verifikasi</span>
                </a>
                <a href="{{ route('staf-legal.verifikasi-berkas.riwayat') }}" class="h-11 rounded-xl px-4 text-sm font-medium flex items-center gap-3 relative transition {{ request()->routeIs('staf-legal.verifikasi-berkas.riwayat') ? 'bg-[#1e3a8a] text-white' : 'text-[#cbd5e1] hover:bg-[#132240]' }}">
                    <span>Riwayat Verifikasi</span>
                </a>
            @endif
        </nav>
        
        <!-- Drawer Footer (Profile Box style) -->
        <div class="pb-5 px-3">
            <div class="bg-[#132240] rounded-xl px-4 py-3 flex gap-3 items-center">
                <div class="bg-[#1e3a8a] border border-[#2d5099] rounded-full w-9 h-9 shrink-0 flex items-center justify-center text-white font-bold text-xs">
                    {{ $initials }}
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-semibold text-white text-sm truncate leading-tight">{{ $user->nama }}</span>
                    <span class="text-[#7c9cc5] text-xs mt-0.5 leading-none capitalize">
                        {{ str_replace('_', ' ', $user->role) }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
