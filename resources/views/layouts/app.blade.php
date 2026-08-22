<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false, sidebarOpen: false }" x-init="
    darkMode = localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
    $watch('darkMode', value => localStorage.setItem('darkMode', value))
" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Kost Management') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
        </style>
        <!-- Dark mode init script to prevent flicker -->
        <script>
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>
    </head>
    <body class="font-sans antialiased bg-gray-50/70 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-200 selection:bg-indigo-500 selection:text-white">
        <div class="flex h-screen overflow-hidden bg-gray-50/70 dark:bg-gray-950">
            
            <!-- Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Topbar -->
                <header class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-md border-b border-gray-200/80 dark:border-gray-800 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-10 transition-colors duration-200 shrink-0">
                    <!-- Mobile Menu Button & Brand -->
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-xl text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none" aria-label="Open menu">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>

                        <!-- Page Title (Desktop only) -->
                        <div class="hidden lg:block font-bold text-lg text-gray-900 dark:text-white tracking-tight">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                    </div>
                    
                    <!-- Topbar Right (Dark Mode Toggle, Notifications & User Dropdown) -->
                    <div class="flex items-center space-x-2 sm:space-x-3 ml-auto">
                        
                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button @click="open = ! open" class="relative p-2.5 rounded-xl text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors focus:outline-none" aria-label="Notifications">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="absolute top-2 right-2 block h-2 w-2 rounded-full ring-2 ring-white dark:ring-gray-900 bg-rose-500 animate-pulse"></span>
                                @endif
                            </button>
                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-200" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-75" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                 class="absolute right-0 z-50 mt-2 w-80 rounded-2xl shadow-xl py-1 bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80 overflow-hidden" 
                                 style="display: none;">
                                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                                    <span class="font-bold text-xs uppercase tracking-wider text-gray-700 dark:text-gray-200">Notifikasi</span> 
                                    @if(Auth::user()->unreadNotifications->count() > 0)
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold leading-none text-rose-700 bg-rose-100 dark:bg-rose-950/60 dark:text-rose-300 rounded-full">{{ Auth::user()->unreadNotifications->count() }} Baru</span>
                                    @endif
                                </div>
                                <div class="max-h-72 overflow-y-auto divide-y divide-gray-100 dark:divide-gray-700/60">
                                    @forelse(Auth::user()->notifications->take(5) as $notification)
                                        <a href="{{ route('notifications.index') }}" class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ is_null($notification->read_at) ? 'bg-indigo-50/40 dark:bg-indigo-950/20' : '' }}">
                                            <p class="text-sm text-gray-800 dark:text-gray-200 font-semibold leading-snug">{{ $notification->data['title'] ?? '' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 line-clamp-2">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                                        </a>
                                    @empty
                                        <div class="px-4 py-6 text-sm text-gray-500 dark:text-gray-400 text-center">Belum ada notifikasi</div>
                                    @endforelse
                                </div>
                                <a href="{{ route('notifications.index') }}" class="block px-4 py-2.5 text-center text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-bold bg-gray-50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700">
                                    Lihat Semua Notifikasi →
                                </a>
                            </div>
                        </div>

                        <!-- Dark Mode Toggle -->
                        <button @click="darkMode = !darkMode" class="p-2.5 rounded-xl text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors focus:outline-none" aria-label="Toggle dark mode">
                            <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                            <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </button>

                        <!-- Settings / Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
                            <button @click="open = ! open" class="inline-flex items-center gap-2 p-1 sm:px-3 sm:py-1.5 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none border border-transparent sm:border-gray-200/80 sm:dark:border-gray-700/80">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-indigo-600 to-indigo-500 flex items-center justify-center text-white font-bold text-xs shadow-xs">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div class="hidden md:block text-left">
                                    <div class="font-semibold text-xs text-gray-800 dark:text-gray-200 leading-tight">{{ Auth::user()->name }}</div>
                                    <div class="text-[11px] text-gray-400 dark:text-gray-500 capitalize leading-tight">{{ Auth::user()->role->value }}</div>
                                </div>
                                <svg class="hidden sm:block h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <div x-show="open" 
                                 x-transition:enter="transition ease-out duration-150" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:enter-end="transform opacity-100 scale-100" 
                                 x-transition:leave="transition ease-in duration-100" 
                                 x-transition:leave-start="transform opacity-100 scale-100" 
                                 x-transition:leave-end="transform opacity-0 scale-95" 
                                 class="absolute right-0 z-50 mt-2 w-52 rounded-2xl shadow-xl py-1.5 bg-white dark:bg-gray-800 border border-gray-200/80 dark:border-gray-700/80" 
                                 style="display: none;">
                                <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                    <p class="text-xs font-semibold text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[11px] text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700/80 transition-colors">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Profil Saya
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Keluar / Log Out
                                    </a>
                                </form>
                            </div>
                        </div>

                    </div>
                </header>

                <!-- Page Content (Scrollable & Responsive) -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50/70 dark:bg-gray-950 p-4 sm:p-6 lg:p-8">
                    @isset($header)
                        <div class="lg:hidden mb-4 font-bold text-xl text-gray-900 dark:text-white tracking-tight">
                            {{ $header }}
                        </div>
                    @endisset
                    {{ $slot }}
                </main>
            </div>
        </div>
        
        <!-- Global Confirm Delete Modal -->
        <x-confirm-delete-modal />
    </body>
</html>
