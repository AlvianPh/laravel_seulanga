<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <div>
                <h2 class="font-black text-2xl text-gray-900 dark:text-white tracking-tight">
                    {{ __('Dashboard') }}
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 border border-gray-200/80 dark:border-gray-700/80 shadow-xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2"></span>
                    {{ now()->translatedFormat('l, d F Y') }}
                </span>
            </div>
        </div>
    </x-slot>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6">

        <!-- Welcome Banner (Modern SaaS Card) -->
        <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg shadow-indigo-500/10 relative overflow-hidden">
            <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute right-20 -bottom-10 w-48 h-48 bg-indigo-400/20 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-indigo-100 mb-3 border border-white/10">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sistem Berjalan Normal
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-indigo-100 text-xs sm:text-sm mt-1 max-w-xl">Kelola hunian, pantau tagihan sewa, dan evaluasi laba bersih bisnis kost Anda dengan mudah.</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold bg-white/20 backdrop-blur-md text-white border border-white/20 capitalize shadow-xs">
                        <svg class="mr-1.5 h-3.5 w-3.5 text-indigo-200" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                        Role: {{ Auth::user()->role->value }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Actions Bar (Aksi Cepat 1-Klik) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-700/70 shadow-xs">
            <div class="flex items-center gap-2">
                <span class="p-2 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 rounded-xl">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Pintasan Cepat:</span>
            </div>
            <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 sm:gap-3">
                <x-ui.button href="{{ route('payments.create') }}" variant="success" size="sm" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Catat Bayar
                </x-ui.button>
                <x-ui.button href="{{ route('contracts.create') }}" variant="primary" size="sm" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Kontrak Baru
                </x-ui.button>
                <x-ui.button href="{{ route('expenses.create') }}" variant="secondary" size="sm" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Pengeluaran
                </x-ui.button>
                <x-ui.button href="{{ route('rooms.create') }}" variant="secondary" size="sm" class="w-full sm:w-auto">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4h2a1 1 0 011 1v4h-4v-4a1 1 0 011-1z"></path></svg>
                    + Kamar
                </x-ui.button>
            </div>
        </div>

        @if(isset($urgentActions) && $urgentActions['totalUrgentCount'] > 0)
        <!-- Urgent Action Widget (Perlu Tindakan Hari Ini) -->
        <div class="bg-amber-50/70 dark:bg-amber-950/30 rounded-2xl border border-amber-200/80 dark:border-amber-800/50 p-5 sm:p-6 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 bg-amber-100 dark:bg-amber-900/60 text-amber-700 dark:text-amber-300 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </span>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">Perlu Tindakan Operasional Segera</h4>
                        <p class="text-xs text-amber-700/80 dark:text-amber-400">Terdapat {{ $urgentActions['totalUrgentCount'] }} item yang memerlukan perhatian Anda</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- 1. Pembayaran Menunggu Verifikasi -->
                @if($urgentActions['pendingPayments']->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-amber-200/60 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                                Pembayaran Pending
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300">
                                {{ $urgentActions['pendingPayments']->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Terdapat pembayaran dari penghuni yang belum diverifikasi.</p>
                    </div>
                    <a href="{{ route('payments.index') }}" class="inline-flex items-center justify-between text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <span>Verifikasi Pembayaran</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                @endif

                <!-- 2. Tagihan Overdue -->
                @if($urgentActions['overdueInvoices']->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-amber-200/60 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                Tagihan Menunggak
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300">
                                {{ $urgentActions['overdueInvoices']->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Tagihan telah melewati batas jatuh tempo dan belum lunas.</p>
                    </div>
                    <a href="{{ route('invoices.index') }}" class="inline-flex items-center justify-between text-xs font-bold text-rose-600 dark:text-rose-400 hover:text-rose-700 dark:hover:text-rose-300 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <span>Follow-up Tagihan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                @endif

                <!-- 3. Kontrak Hampir Habis -->
                @if($urgentActions['expiringContracts']->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-amber-200/60 dark:border-gray-700 shadow-xs flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold text-gray-700 dark:text-gray-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                Kontrak Habis (≤14 Hari)
                            </span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-sky-100 dark:bg-sky-950/60 text-sky-700 dark:text-sky-300">
                                {{ $urgentActions['expiringContracts']->count() }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Kontrak sewa akan segera berakhir dalam 2 pekan ke depan.</p>
                    </div>
                    <a href="{{ route('contracts.index') }}" class="inline-flex items-center justify-between text-xs font-bold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 pt-2 border-t border-gray-100 dark:border-gray-700">
                        <span>Konfirmasi Perpanjangan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Stats Row 1: Operational KPIs (Kamar, Penghuni, Tagihan, Kas Masuk) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Kamar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Unit Kamar</p>
                        <h4 class="text-3xl font-black text-gray-900 dark:text-white mt-1.5 tracking-tight">{{ $stats['totalRooms'] }} <span class="text-sm font-semibold text-gray-400">Unit</span></h4>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/60 rounded-2xl text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4h2a1 1 0 011 1v4h-4v-4a1 1 0 011-1z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs font-bold">
                    <span class="inline-flex items-center text-emerald-600 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                        {{ $stats['availableRooms'] }} Kosong
                    </span>
                    <span class="inline-flex items-center text-indigo-600 dark:text-indigo-400">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 mr-1.5"></span>
                        {{ $stats['occupiedRooms'] }} Terisi ({{ $stats['occupancyRate'] }}%)
                    </span>
                </div>
            </div>

            <!-- Penghuni Aktif -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Penghuni Aktif</p>
                        <h4 class="text-3xl font-black text-gray-900 dark:text-white mt-1.5 tracking-tight">{{ $stats['activeTenants'] }} <span class="text-sm font-semibold text-gray-400">Orang</span></h4>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-2xl text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Tercatat dalam kontrak sewa
                </div>
            </div>

            <!-- Tagihan Jatuh Tempo -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Tagihan Jatuh Tempo</p>
                        <h4 class="text-3xl font-black text-rose-600 dark:text-rose-400 mt-1.5 tracking-tight">{{ $stats['dueInvoices'] }} <span class="text-sm font-semibold text-gray-400">Invoice</span></h4>
                    </div>
                    <div class="p-3 bg-rose-50 dark:bg-rose-950/60 rounded-2xl text-rose-600 dark:text-rose-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center text-xs text-rose-600 dark:text-rose-400 font-semibold">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Perlu ditagih / diverifikasi
                </div>
            </div>

            <!-- Kas Masuk Hari Ini -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kas Masuk Hari Ini</p>
                        <h4 class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1.5 font-mono tracking-tight">Rp {{ number_format($stats['paymentsToday'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 bg-sky-50 dark:bg-sky-950/60 rounded-2xl text-sky-600 dark:text-sky-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-1 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Pembayaran masuk hari ini
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SECTION 2: Dedicated Financial Cash Flow & Net Profit Performance Center -->
        <!-- ========================================================================= -->
        <div class="space-y-6 pt-2">
            
            <!-- Section Header & Monthly Financial Overview Cards -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white tracking-tight flex items-center gap-2">
                        <span>Laporan Keuangan & Arus Kas</span>
                        <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60">
                            Bulan Ini ({{ now()->translatedFormat('F Y') }})
                        </span>
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Analisis pendapatan terverifikasi, pengeluaran operasional, dan margin laba bersih</p>
                </div>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                    <span>Buka Rekap Laporan Lengkap</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Financial Metric Cards (3 Cards) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
                <!-- 1. Pendapatan Bulan Ini -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-emerald-100 dark:border-emerald-950/60 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 h-1 w-full bg-emerald-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">Total Pendapatan</p>
                            <h4 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1.5 font-mono tracking-tight">
                                Rp {{ number_format($financials['income'], 0, ',', '.') }}
                            </h4>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Pembayaran sewa terverifikasi</p>
                        </div>
                        <div class="p-2.5 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl text-emerald-600 dark:text-emerald-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- 2. Pengeluaran Bulan Ini -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border border-rose-100 dark:border-rose-950/60 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 h-1 w-full bg-rose-500"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400">Total Pengeluaran</p>
                            <h4 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white mt-1.5 font-mono tracking-tight">
                                Rp {{ number_format($financials['expense'], 0, ',', '.') }}
                            </h4>
                            <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Biaya operasional & utilitas</p>
                        </div>
                        <div class="p-2.5 bg-rose-50 dark:bg-rose-950/60 rounded-xl text-rose-600 dark:text-rose-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- 3. Laba Bersih Bulan Ini -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 border {{ $financials['profit'] >= 0 ? 'border-indigo-100 dark:border-indigo-950/60' : 'border-rose-200 dark:border-rose-900/60' }} shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 left-0 h-1 w-full {{ $financials['profit'] >= 0 ? 'bg-indigo-600' : 'bg-rose-600' }}"></div>
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="flex items-center gap-1.5">
                                <p class="text-xs font-bold uppercase tracking-wider {{ $financials['profit'] >= 0 ? 'text-indigo-700 dark:text-indigo-400' : 'text-rose-700 dark:text-rose-400' }}">
                                    Laba Bersih (Net Profit)
                                </p>
                            </div>
                            <h4 class="text-2xl sm:text-3xl font-black {{ $financials['profit'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400' }} mt-1.5 font-mono tracking-tight">
                                Rp {{ number_format($financials['profit'], 0, ',', '.') }}
                            </h4>
                            @php
                                $margin = $financials['income'] > 0 ? round(($financials['profit'] / $financials['income']) * 100, 1) : 0;
                            @endphp
                            <p class="text-[11px] font-semibold {{ $financials['profit'] >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400' }} mt-1">
                                Net Margin: {{ $margin }}%
                            </p>
                        </div>
                        <div class="p-2.5 {{ $financials['profit'] >= 0 ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400' : 'bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400' }} rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Visualizer Tabs & Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ activeTab: 'cashflow' }">
                
                <!-- Main Financial Visualizer (Span 2) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/70 p-5 sm:p-6 lg:col-span-2 flex flex-col justify-between">
                    <div>
                        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3 mb-6">
                            <div>
                                <h4 class="text-base font-bold text-gray-900 dark:text-white" x-text="activeTab === 'cashflow' ? 'Visualisasi Arus Kas (Pendapatan vs Pengeluaran)' : (activeTab === 'profit' ? 'Grafik Pertumbuhan Tren Laba Bersih' : 'Rincian Data Finansial 6 Bulan')"></h4>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Pergerakan finansial berkala 6 bulan terakhir</p>
                            </div>
                            
                            <!-- Tab Switcher (Modern Segmented Control) -->
                            <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-900 rounded-xl text-xs font-semibold self-start sm:self-auto">
                                <button type="button" @click="activeTab = 'cashflow'" :class="activeTab === 'cashflow' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1.5 rounded-lg transition-all">
                                    Arus Kas
                                </button>
                                <button type="button" @click="activeTab = 'profit'" :class="activeTab === 'profit' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1.5 rounded-lg transition-all">
                                    Tren Laba
                                </button>
                                <button type="button" @click="activeTab = 'table'" :class="activeTab === 'table' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 shadow-xs font-bold' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400'" class="px-3 py-1.5 rounded-lg transition-all">
                                    Tabel Rincian
                                </button>
                            </div>
                        </div>

                        <!-- 1. Cashflow Bar Chart View -->
                        <div x-show="activeTab === 'cashflow'" class="space-y-4">
                            <div class="relative h-72 w-full">
                                <canvas id="cashflowChart"></canvas>
                            </div>
                        </div>

                        <!-- 2. Profit Line Chart View -->
                        <div x-show="activeTab === 'profit'" x-cloak class="space-y-4">
                            <div class="relative h-72 w-full">
                                <canvas id="profitChart"></canvas>
                            </div>
                        </div>

                        <!-- 3. Tabular Data View (Super scan-friendly for Owners) -->
                        <div x-show="activeTab === 'table'" x-cloak class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="border-b border-gray-200 dark:border-gray-700 bg-gray-50/70 dark:bg-gray-800/60 text-gray-500 dark:text-gray-400 uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="py-2.5 px-3">Periode</th>
                                        <th class="py-2.5 px-3 text-right text-emerald-600 dark:text-emerald-400">Pendapatan</th>
                                        <th class="py-2.5 px-3 text-right text-rose-600 dark:text-rose-400">Pengeluaran</th>
                                        <th class="py-2.5 px-3 text-right">Laba Bersih</th>
                                        <th class="py-2.5 px-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                                    @foreach($chartData['labels'] as $index => $label)
                                    @php
                                        $inc = $chartData['incomes'][$index] ?? 0;
                                        $exp = $chartData['expenses'][$index] ?? 0;
                                        $prf = $chartData['profits'][$index] ?? 0;
                                    @endphp
                                    <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                        <td class="py-3 px-3 font-bold text-gray-900 dark:text-white">{{ $label }}</td>
                                        <td class="py-3 px-3 text-right font-mono text-emerald-600 dark:text-emerald-400 font-semibold">Rp {{ number_format($inc, 0, ',', '.') }}</td>
                                        <td class="py-3 px-3 text-right font-mono text-rose-600 dark:text-rose-400 font-semibold">Rp {{ number_format($exp, 0, ',', '.') }}</td>
                                        <td class="py-3 px-3 text-right font-mono font-bold {{ $prf >= 0 ? 'text-indigo-600 dark:text-indigo-400' : 'text-rose-600 dark:text-rose-400' }}">
                                            Rp {{ number_format($prf, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            @if($prf > 0)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">Surplus</span>
                                            @elseif($prf < 0)
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300">Defisit</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">Nol</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Indicator Footer -->
                    <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex flex-wrap items-center justify-between gap-2 text-xs text-gray-400 dark:text-gray-500">
                        <div class="flex items-center gap-4">
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Pendapatan</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Pengeluaran</span>
                            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span> Laba Bersih</span>
                        </div>
                        <span class="text-[11px]">Diperbarui otomatis dari transaksi</span>
                    </div>
                </div>

                <!-- Occupancy & Room Status Donut Chart (Span 1) -->
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/70 p-5 sm:p-6 flex flex-col justify-between">
                    <div>
                        <div class="mb-4">
                            <h4 class="text-base font-bold text-gray-900 dark:text-white">Rasio Okupansi Hunian</h4>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Persentase keterisian kamar aktif saat ini</p>
                        </div>
                        
                        <div class="flex-grow flex flex-col justify-center items-center relative py-4">
                            <div class="relative h-48 w-full max-w-[200px]">
                                <canvas id="occupancyChart"></canvas>
                            </div>
                            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                                <span class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['occupancyRate'] }}%</span>
                                <span class="text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider mt-0.5">Tingkat Terisi</span>
                            </div>
                        </div>
                    </div>

                    <!-- Clean Room Breakdown Legend -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 mr-2"></span>
                                <span class="text-gray-600 dark:text-gray-300 font-medium">Kamar Terisi</span>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white font-mono">{{ $stats['occupiedRooms'] }} Unit</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span>
                                <span class="text-gray-600 dark:text-gray-300 font-medium">Kamar Kosong / Siap</span>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white font-mono">{{ $stats['availableRooms'] }} Unit</span>
                        </div>
                        @php
                            $maintenanceRooms = $stats['totalRooms'] - ($stats['availableRooms'] + $stats['occupiedRooms']);
                        @endphp
                        @if($maintenanceRooms > 0)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 mr-2"></span>
                                <span class="text-gray-600 dark:text-gray-300 font-medium">Dalam Perbaikan</span>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white font-mono">{{ $maintenanceRooms }} Unit</span>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Chart Configuration Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const isDarkMode = document.documentElement.classList.contains('dark') || window.matchMedia('(prefers-color-scheme: dark)').matches;
            
            const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
            const textColor = isDarkMode ? 'rgba(156, 163, 175, 1)' : 'rgba(107, 114, 128, 1)'; 
            
            const chartLabels = {!! json_encode($chartData['labels']) !!};
            const incomes = {!! json_encode($chartData['incomes']) !!};
            const expenses = {!! json_encode($chartData['expenses']) !!};
            const profits = {!! json_encode($chartData['profits']) !!};

            Chart.defaults.color = textColor;
            Chart.defaults.font.family = "'Inter', system-ui, -apple-system, sans-serif";
            
            const formatRupiahK = (val) => {
                if (Math.abs(val) >= 1000000) {
                    return 'Rp ' + (val / 1000000).toLocaleString('id-ID', { maximumFractionDigits: 1 }) + ' Jt';
                } else if (Math.abs(val) >= 1000) {
                    return 'Rp ' + (val / 1000).toLocaleString('id-ID') + ' Rb';
                }
                return 'Rp ' + val.toLocaleString('id-ID');
            };

            const tooltipConfig = {
                backgroundColor: isDarkMode ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.98)',
                titleColor: isDarkMode ? '#fff' : '#111827',
                bodyColor: isDarkMode ? '#e5e7eb' : '#374151',
                borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)',
                borderWidth: 1,
                padding: 12,
                boxPadding: 6,
                usePointStyle: true,
                titleFont: { size: 12, weight: 'bold' },
                bodyFont: { size: 12 }
            };

            // 1. Cashflow Bar Chart
            const ctxCashflow = document.getElementById('cashflowChart').getContext('2d');
            const cashflowChartInstance = new Chart(ctxCashflow, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: incomes,
                            backgroundColor: 'rgba(16, 185, 129, 0.85)', // emerald-500
                            hoverBackgroundColor: 'rgba(16, 185, 129, 1)',
                            borderRadius: 6,
                            barPercentage: 0.55,
                            categoryPercentage: 0.75
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenses,
                            backgroundColor: 'rgba(244, 63, 94, 0.85)', // rose-500
                            hoverBackgroundColor: 'rgba(244, 63, 94, 1)',
                            borderRadius: 6,
                            barPercentage: 0.55,
                            categoryPercentage: 0.75
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: {
                                callback: function(value) { return formatRupiahK(value); },
                                font: { size: 11 }
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' } },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: { 
                            position: 'top', 
                            align: 'end', 
                            labels: { usePointStyle: true, boxWidth: 8, font: { weight: 'bold', size: 11 } } 
                        },
                        tooltip: {
                            ...tooltipConfig,
                            callbacks: {
                                label: function(context) { 
                                    return ' ' + context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString('id-ID'); 
                                }
                            }
                        }
                    }
                }
            });

            // 2. Profit Line Chart
            const ctxProfit = document.getElementById('profitChart').getContext('2d');
            let gradient = ctxProfit.createLinearGradient(0, 0, 0, 260);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)'); // indigo-500
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

            const profitChartInstance = new Chart(ctxProfit, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Laba Bersih',
                        data: profits,
                        borderColor: 'rgb(79, 70, 229)', // indigo-600
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: 'rgb(255, 255, 255)',
                        pointBorderColor: 'rgb(79, 70, 229)',
                        pointBorderWidth: 2.5,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        fill: true,
                        tension: 0.35
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            grid: { color: gridColor, drawBorder: false },
                            ticks: {
                                callback: function(value) { return formatRupiahK(value); },
                                font: { size: 11 }
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '600' } },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipConfig,
                            callbacks: {
                                label: function(context) { 
                                    const val = context.parsed.y;
                                    return ' Laba Bersih: Rp ' + val.toLocaleString('id-ID'); 
                                }
                            }
                        }
                    }
                }
            });

            // 3. Occupancy Doughnut Chart
            const occAvailable = {{ $stats['availableRooms'] }};
            const occOccupied = {{ $stats['occupiedRooms'] }};
            const occMaintenance = Math.max(0, {{ $stats['totalRooms'] }} - (occAvailable + occOccupied));

            const ctxOccupancy = document.getElementById('occupancyChart').getContext('2d');
            new Chart(ctxOccupancy, {
                type: 'doughnut',
                data: {
                    labels: ['Terisi', 'Kosong', 'Perbaikan'],
                    datasets: [{
                        data: [occOccupied, occAvailable, occMaintenance],
                        backgroundColor: [
                            'rgb(79, 70, 229)', // indigo-600
                            'rgb(16, 185, 129)', // emerald-500
                            'rgb(245, 158, 11)'  // amber-500
                        ],
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '78%',
                    plugins: {
                        legend: { display: false },
                        tooltip: tooltipConfig
                    }
                }
            });
            
            // Watch for Dark Mode Changes in Alpine to redraw charts
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.attributeName === 'class') {
                        const isDark = document.documentElement.classList.contains('dark');
                        const newGridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
                        const newTextColor = isDark ? 'rgba(156, 163, 175, 1)' : 'rgba(107, 114, 128, 1)';
                        
                        Chart.instances.forEach(chart => {
                            if(chart.options.scales && chart.options.scales.y) {
                                chart.options.scales.y.grid.color = newGridColor;
                            }
                            chart.options.color = newTextColor;
                            
                            // Update Tooltips
                            chart.options.plugins.tooltip.backgroundColor = isDark ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.98)';
                            chart.options.plugins.tooltip.titleColor = isDark ? '#fff' : '#111827';
                            chart.options.plugins.tooltip.bodyColor = isDark ? '#e5e7eb' : '#374151';
                            chart.options.plugins.tooltip.borderColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.08)';
                            
                            chart.update();
                        });
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        });
    </script>
</x-app-layout>
