<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6">

        <!-- Welcome Banner (Modern SaaS Card) -->
        <div class="bg-gradient-to-r from-indigo-600 via-indigo-700 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg shadow-indigo-500/10 relative overflow-hidden">
            <!-- Background subtle pattern/circle -->
            <div class="absolute -right-10 -top-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute right-20 -bottom-10 w-48 h-48 bg-indigo-400/20 rounded-full blur-xl pointer-events-none"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold text-indigo-100 mb-3 border border-white/10">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Sistem Aktif & Terhubung
                    </div>
                    <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                    <p class="text-indigo-100 text-sm sm:text-base mt-1 max-w-xl">Ringkasan performa bisnis dan aktivitas operasional kost Anda hari ini.</p>
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
                <span class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Aksi Cepat:</span>
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

        <!-- Stats Row 1: Kamar, Penghuni, Tagihan, Kas Masuk (Clean Responsive Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Total Kamar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Total Kamar</p>
                        <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">{{ $stats['totalRooms'] }}</h4>
                    </div>
                    <div class="p-3 bg-indigo-50 dark:bg-indigo-950/60 rounded-xl text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4h2a1 1 0 011 1v4h-4v-4a1 1 0 011-1z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between text-xs">
                    <span class="inline-flex items-center text-emerald-600 dark:text-emerald-400 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                        {{ $stats['availableRooms'] }} Kosong
                    </span>
                    <span class="inline-flex items-center text-indigo-600 dark:text-indigo-400 font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 mr-1.5"></span>
                        {{ $stats['occupiedRooms'] }} Terisi
                    </span>
                </div>
            </div>

            <!-- Penghuni Aktif -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Penghuni Aktif</p>
                        <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">{{ $stats['activeTenants'] }}</h4>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-950/60 rounded-xl text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-1 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Memiliki kontrak sewa aktif
                </div>
            </div>

            <!-- Tagihan Jatuh Tempo -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Menunggak</p>
                        <h4 class="text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">{{ $stats['dueInvoices'] }}</h4>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-950/60 rounded-xl text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center text-xs text-amber-600 dark:text-amber-400 font-medium">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Jatuh tempo & perlu tindak lanjut
                </div>
            </div>

            <!-- Kas Masuk Hari Ini -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-5 sm:p-6 border border-gray-200/80 dark:border-gray-700/70 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Kas Masuk Hari Ini</p>
                        <h4 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5 tracking-tight">Rp {{ number_format($stats['paymentsToday'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-3 bg-sky-50 dark:bg-sky-950/60 rounded-xl text-sky-600 dark:text-sky-400 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center text-xs text-gray-500 dark:text-gray-400">
                    <svg class="w-4 h-4 mr-1 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Pembayaran terverifikasi
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cash Flow Chart (Span 2) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/70 p-5 sm:p-6 lg:col-span-2">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 mb-6">
                    <div>
                        <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Arus Kas Keuangan</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Perbandingan Pendapatan & Pengeluaran 6 Bulan Terakhir</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 self-start sm:self-auto">
                        6 Bulan
                    </span>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>

            <!-- Occupancy Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/70 p-5 sm:p-6 flex flex-col justify-between">
                <div class="mb-4">
                    <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Tingkat Keterisian</h4>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Rasio okupansi kamar saat ini</p>
                </div>
                <div class="flex-grow flex flex-col justify-center items-center relative py-4">
                    <div class="relative h-48 w-full max-w-[200px]">
                        <canvas id="occupancyChart"></canvas>
                    </div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                        <span class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">{{ $stats['occupancyRate'] }}%</span>
                        <span class="text-[11px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-wider mt-0.5">Terisi</span>
                    </div>
                </div>
                <!-- Small legend -->
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700/60 grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-indigo-600 mr-2"></span><span class="text-gray-600 dark:text-gray-300 font-medium">Terisi ({{ $stats['occupiedRooms'] }})</span></div>
                    <div class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-slate-300 dark:bg-slate-600 mr-2"></span><span class="text-gray-600 dark:text-gray-300 font-medium">Kosong ({{ $stats['availableRooms'] }})</span></div>
                </div>
            </div>

            <!-- Profit Statement Line -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/70 p-5 sm:p-6 lg:col-span-3">
                <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-2 mb-6">
                    <div>
                        <h4 class="text-base sm:text-lg font-bold text-gray-900 dark:text-white">Tren Laba Bersih Bulanan</h4>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Laba bersih operasional setelah dikurangi seluruh pengeluaran</p>
                    </div>
                    <span class="inline-flex items-center px-3.5 py-1.5 bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 rounded-xl text-xs font-bold self-start sm:self-auto shadow-xs">
                        Bulan ini: Rp {{ number_format($financials['profit'], 0, ',', '.') }}
                    </span>
                </div>
                <div class="relative h-64 w-full">
                    <canvas id="profitChart"></canvas>
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
            Chart.defaults.font.family = "'Inter', 'Nunito', sans-serif";
            const tooltipConfig = {
                backgroundColor: isDarkMode ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.95)',
                titleColor: isDarkMode ? '#fff' : '#111827',
                bodyColor: isDarkMode ? '#e5e7eb' : '#4b5563',
                borderColor: isDarkMode ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                borderWidth: 1,
                padding: 12,
                boxPadding: 6,
                usePointStyle: true
            };

            // 1. Cashflow Bar Chart
            const ctxCashflow = document.getElementById('cashflowChart').getContext('2d');
            new Chart(ctxCashflow, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: incomes,
                            backgroundColor: 'rgba(16, 185, 129, 0.9)', // emerald-500
                            borderRadius: 4,
                            barPercentage: 0.5,
                            categoryPercentage: 0.8
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenses,
                            backgroundColor: 'rgba(244, 63, 94, 0.9)', // rose-500
                            borderRadius: 4,
                            barPercentage: 0.5,
                            categoryPercentage: 0.8
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
                                callback: function(value) { return 'Rp ' + (value/1000).toLocaleString() + 'k'; },
                                padding: 10
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: {weight: 'bold'} } },
                        tooltip: {
                            ...tooltipConfig,
                            callbacks: {
                                label: function(context) { return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString(); }
                            }
                        }
                    }
                }
            });

            // 2. Profit Line Chart
            const ctxProfit = document.getElementById('profitChart').getContext('2d');
            let gradient = ctxProfit.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)'); // indigo-600
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

            new Chart(ctxProfit, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Laba Bersih',
                        data: profits,
                        borderColor: 'rgb(79, 70, 229)', // indigo-600
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: 'rgb(255, 255, 255)',
                        pointBorderColor: 'rgb(79, 70, 229)',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: gridColor, drawBorder: false },
                            ticks: {
                                callback: function(value) { return 'Rp ' + (value/1000).toLocaleString() + 'k'; },
                                padding: 10
                            },
                            border: { display: false }
                        },
                        x: {
                            grid: { display: false },
                            border: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            ...tooltipConfig,
                            displayColors: false,
                            callbacks: {
                                label: function(context) { return 'Laba: Rp ' + context.parsed.y.toLocaleString(); }
                            }
                        }
                    }
                }
            });

            // 3. Occupancy Doughnut Chart
            const occAvailable = {{ $stats['availableRooms'] }};
            const occOccupied = {{ $stats['occupiedRooms'] }};
            const occMaintenance = {{ $stats['totalRooms'] }} - (occAvailable + occOccupied);

            const ctxOccupancy = document.getElementById('occupancyChart').getContext('2d');
            new Chart(ctxOccupancy, {
                type: 'doughnut',
                data: {
                    labels: ['Terisi', 'Kosong', 'Lainnya'],
                    datasets: [{
                        data: [occOccupied, occAvailable, occMaintenance],
                        backgroundColor: [
                            'rgb(79, 70, 229)', // indigo-600
                            isDarkMode ? 'rgba(75, 85, 99, 0.5)' : 'rgb(229, 231, 235)', // gray
                            'rgb(245, 158, 11)'  // amber-500
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '80%',
                    plugins: {
                        legend: { display: false }, // Using custom HTML legend below the chart
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
                            chart.options.plugins.tooltip.backgroundColor = isDark ? 'rgba(17, 24, 39, 0.95)' : 'rgba(255, 255, 255, 0.95)';
                            chart.options.plugins.tooltip.titleColor = isDark ? '#fff' : '#111827';
                            chart.options.plugins.tooltip.bodyColor = isDark ? '#e5e7eb' : '#4b5563';
                            chart.options.plugins.tooltip.borderColor = isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)';
                            
                            // Update Occupancy Empty Color
                            if (chart.canvas.id === 'occupancyChart') {
                                chart.data.datasets[0].backgroundColor[1] = isDark ? 'rgba(75, 85, 99, 0.5)' : 'rgb(229, 231, 235)';
                            }
                            
                            chart.update();
                        });
                    }
                });
            });
            observer.observe(document.documentElement, { attributes: true });
        });
    </script>
</x-app-layout>
