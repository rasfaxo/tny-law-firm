@props([
    'title' => null,
    'breadcrumbs' => null,
])

@php
    $user = Auth::user();
    $initials = '';
    if ($user && $user->nama) {
        $initials = collect(explode(' ', $user->nama))
            ->map(fn($n) => $n[0] ?? '')
            ->take(2)
            ->join('');
    }
    
    $roleLabel = '';
    $roleBadgeClass = 'bg-gray-100 text-gray-800';
    $avatarBg = 'bg-gray-800';

    if ($user) {
        $roleLabel = match ($user->role) {
            'klien' => 'Klien',
            'admin' => 'Admin',
            'staf_legal' => 'Staf Legal',
            default => ucfirst($user->role),
        };

        $roleBadgeClass = match ($user->role) {
            'klien' => 'bg-blue-50 text-blue-700',
            'admin' => 'bg-purple-50 text-purple-700',
            'staf_legal' => 'bg-green-50 text-green-700',
            default => 'bg-gray-100 text-gray-800',
        };

        $avatarBg = match ($user->role) {
            'klien' => 'bg-[#1e3a8a]',
            'admin' => 'bg-[#6d28d9]',
            'staf_legal' => 'bg-[#15803d]',
            default => 'bg-gray-800',
        };
    }
@endphp

<header class="bg-white border-b border-[#E2E8F0] h-20 flex items-center justify-between px-6 md:px-8 shrink-0 z-10 drop-shadow-sm">
    <div class="flex items-center gap-4 flex-1">
        <!-- Mobile Hamburger Button -->
        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none lg:hidden shrink-0">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        @if(isset($header) && $header->isNotEmpty())
            <!-- If custom header is provided, output it directly (backward compatibility) -->
            <div class="w-full flex items-center justify-between">
                {{ $header }}
            </div>
        @else
            <!-- Standard dynamic breadcrumbs and title -->
            <div class="flex flex-col">
                @if(!empty($breadcrumbs))
                    <nav class="flex items-center gap-1.5 text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5 select-none">
                        @foreach($breadcrumbs as $index => $crumb)
                            @if($index > 0)
                                <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            @endif
                            @if(isset($crumb['url']))
                                <a href="{{ $crumb['url'] }}" class="hover:text-accent-blue transition">{{ $crumb['label'] }}</a>
                            @else
                                <span class="text-gray-600">{{ $crumb['label'] }}</span>
                            @endif
                        @endforeach
                    </nav>
                @else
                    <!-- Fallback breadcrumb based on user role -->
                    @if($user)
                        <nav class="flex items-center gap-1 text-xs font-bold text-gray-400 uppercase tracking-wider mb-0.5 select-none">
                            <span>{{ $roleLabel }}</span>
                            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-gray-600">{{ $title ?? 'Dashboard' }}</span>
                        </nav>
                    @endif
                @endif

                <h2 class="font-extrabold text-2xl text-navy-dark leading-tight tracking-tight">
                    {{ $title ?? 'Dashboard' }}
                </h2>
            </div>
        @endif
    </div>

    <!-- User Profile Dropdown (Only rendered if NOT using custom header layout) -->
    @if(!isset($header) || !$header->isNotEmpty())
        @if($user)
            <div class="flex items-center gap-3">
                <!-- Role Badge -->
                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $roleBadgeClass }} hidden sm:inline-block select-none">
                    {{ $roleLabel }}
                </span>

                <!-- Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="bg-white border border-[#E2E8F0] hover:border-accent-blue transition duration-150 rounded-full pl-2 pr-3.5 py-1.5 flex items-center gap-2.5 shadow-sm select-none focus:outline-none">
                            <!-- Avatar circle -->
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-xs font-bold tracking-wider {{ $avatarBg }} shrink-0">
                                {{ strtoupper($initials) }}
                            </div>
                            <!-- Name & Status -->
                            <div class="flex flex-col items-start text-left hidden md:flex">
                                <span class="text-xs font-semibold text-navy-dark leading-none">{{ $user->nama }}</span>
                                <span class="text-xs text-gray-500 leading-none mt-0.5">Akun aktif</span>
                            </div>
                            <!-- Caret -->
                            <svg class="h-3 w-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profil Saya') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Keluar') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        @endif
    @endif
</header>
