<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="space-y-6">

        <!-- Welcome Message (Clean) -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-6 sm:px-8 sm:py-8 flex flex-col md:flex-row justify-between items-center">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Selamat Datang, {{ Auth::user()->name }}!</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Ringkasan performa bisnis Kost Anda hari ini.</p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900/50 dark:text-indigo-300 capitalize">
                        <svg class="mr-1.5 h-4 w-4 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a1 1 0 00-1 1v1a1 1 0 002 0V3a1 1 0 00-1-1zM4 4h3a3 3 0 006 0h3a2 2 0 012 2v9a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2zm2.5 7a1.5 1.5 0 100-3 1.5 1.5 0 000 3zm2.45 4a2.5 2.5 0 10-4.9 0h4.9zM12 9a1 1 0 100 2h3a1 1 0 100-2h-3zm-1 4a1 1 0 011-1h2a1 1 0 110 2h-2a1 1 0 01-1-1z" clip-rule="evenodd" /></svg>
                        Role: {{ Auth::user()->role->value }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats Row 1: Kamar & Penghuni (Clean Cards) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Kamar -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between group hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kamar</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['totalRooms'] }}</h4>
                    </div>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/40 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4h2a1 1 0 011 1v4h-4v-4a1 1 0 011-1z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $stats['availableRooms'] }} Kosong</span>
                    <span class="text-gray-300 dark:text-gray-600 mx-2">|</span> 
                    <span class="text-rose-600 dark:text-rose-400 font-medium">{{ $stats['occupiedRooms'] }} Terisi</span>
                </div>
            </div>

            <!-- Penghuni Aktif -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between group hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penghuni Aktif</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['activeTenants'] }}</h4>
                    </div>
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/40 rounded-lg text-emerald-600 dark:text-emerald-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                    Memiliki kontrak aktif
                </div>
            </div>

            <!-- Tagihan Jatuh Tempo -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between group hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Menunggak</p>
                        <h4 class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $stats['dueInvoices'] }}</h4>
                    </div>
                    <div class="p-2 bg-amber-50 dark:bg-amber-900/40 rounded-lg text-amber-600 dark:text-amber-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                    Jatuh tempo & overdue
                </div>
            </div>

            <!-- Pembayaran Hari Ini -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col justify-between group hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kas Masuk Hari Ini</p>
                        <h4 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($stats['paymentsToday'], 0, ',', '.') }}</h4>
                    </div>
                    <div class="p-2 bg-indigo-50 dark:bg-indigo-900/40 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500 dark:text-gray-400">
                    Pembayaran terverifikasi
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cash Flow Chart (Span 2) -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">Arus Kas Keuangan</h4>
                    <span class="text-sm text-gray-500 dark:text-gray-400">6 Bulan Terakhir</span>
                </div>
                <div class="relative h-72 w-full">
                    <canvas id="cashflowChart"></canvas>
                </div>
            </div>

            <!-- Occupancy Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 flex flex-col">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Tingkat Keterisian</h4>
                <div class="flex-grow flex flex-col justify-center items-center relative">
                    <div class="relative h-48 w-full max-w-[200px]">
                        <canvas id="occupancyChart"></canvas>
                    </div>
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                        <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['occupancyRate'] }}%</span>
                        <span class="text-xs text-gray-500 font-semibold uppercase tracking-wide mt-1">Terisi</span>
                    </div>
                </div>
                <!-- Small legend alternative -->
                <div class="mt-4 grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-indigo-500 mr-2"></span><span class="text-gray-600 dark:text-gray-400">Terisi ({{ $stats['occupiedRooms'] }})</span></div>
                    <div class="flex items-center"><span class="w-3 h-3 rounded-full bg-gray-200 dark:bg-gray-600 mr-2"></span><span class="text-gray-600 dark:text-gray-400">Kosong ({{ $stats['availableRooms'] }})</span></div>
                </div>
            </div>

            <!-- Profit Statement Line -->
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 lg:col-span-3">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white">Tren Laba Bersih</h4>
                    <span class="text-sm font-medium px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg">
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
