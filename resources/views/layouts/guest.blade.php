<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" x-init="
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
    <body class="font-sans antialiased bg-gray-50/70 dark:bg-gray-950 text-gray-900 dark:text-gray-100 transition-colors duration-200 selection:bg-indigo-500 selection:text-white min-h-screen flex flex-col justify-center items-center p-4 sm:p-6">
        
        <!-- Dark Mode Toggle Floating Button -->
        <div class="fixed top-4 right-4 z-50">
            <button @click="darkMode = !darkMode" class="p-2.5 rounded-xl text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors focus:outline-none bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border border-gray-200/80 dark:border-gray-800 shadow-xs" aria-label="Toggle dark mode">
                <svg x-show="!darkMode" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg x-show="darkMode" x-cloak class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </button>
        </div>

        @php
            $setting = \App\Models\Setting::getInstance();
        @endphp
        <div class="mb-6 text-center">
            <a href="/" class="inline-flex flex-col items-center gap-2 group">
                @if($setting->kost_logo)
                    <img src="{{ Storage::url($setting->kost_logo) }}" alt="Logo" class="w-14 h-14 object-cover rounded-2xl shadow-sm">
                @else
                    <div class="w-14 h-14 bg-gradient-to-tr from-indigo-600 to-indigo-500 rounded-2xl flex items-center justify-center text-white font-black text-2xl shadow-md shadow-indigo-500/20 group-hover:scale-105 transition-transform">
                        {{ strtoupper(substr($setting->kost_name ?? 'K', 0, 1)) }}
                    </div>
                @endif
                <div class="mt-2 text-center">
                    <h1 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight leading-tight">{{ $setting->kost_name ?? 'Kost Management' }}</h1>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Management System</p>
                </div>
            </a>
        </div>

        <div class="w-full sm:max-w-md bg-white dark:bg-gray-900 rounded-3xl shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-200/80 dark:border-gray-800 p-6 sm:p-8">
            {{ $slot }}
        </div>
    </body>
</html>
