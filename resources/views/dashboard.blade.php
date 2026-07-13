<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- Load Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Welcome Message -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex justify-between items-center">
                    <div>
                        <h3 class="text-2xl font-bold">Selamat datang kembali, {{ Auth::user()->name }}!</h3>
                        <p class="text-sm text-gray-500 mt-1">Ini adalah ringkasan performa Kost Anda hari ini.</p>
                    </div>
                    <div>
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold uppercase tracking-wider">
                            {{ Auth::user()->role->value }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stats Row 1: Kamar & Penghuni -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Kamar -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Kamar</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['totalRooms'] }}</div>
                    <div class="mt-2 text-xs text-gray-500">
                        <span class="text-green-600 font-semibold">{{ $stats['availableRooms'] }} Kosong</span> • 
                        <span class="text-red-600 font-semibold">{{ $stats['occupiedRooms'] }} Terisi</span>
                    </div>
                </div>

                <!-- Penghuni Aktif -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Penghuni Aktif</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['activeTenants'] }}</div>
                    <div class="mt-2 text-xs text-gray-500">Memiliki kontrak sewa berjalan</div>
                </div>

                <!-- Tagihan Jatuh Tempo -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Tagihan Jatuh Tempo</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $stats['dueInvoices'] }}</div>
                    <div class="mt-2 text-xs text-gray-500">Jatuh tempo dlm 7 hari atau lewat</div>
                </div>

                <!-- Pembayaran Hari Ini -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Kas Masuk Hari Ini</div>
                    <div class="mt-1 text-3xl font-bold text-gray-900 dark:text-white flex items-center">
                        <span class="text-sm mr-1">Rp</span>
                        {{ number_format($stats['paymentsToday'], 0, ',', '.') }}
                    </div>
                    <div class="mt-2 text-xs text-gray-500">Dari pembayaran *Verified*</div>
                </div>
            </div>

            <!-- Stats Row 2: Financials Bulan Ini -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Pendapatan (Bulan Ini)</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($financials['income'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-red-100 text-red-600 mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Pengeluaran (Bulan Ini)</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($financials['expense'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full {{ $financials['profit'] >= 0 ? 'bg-indigo-100 text-indigo-600' : 'bg-red-100 text-red-600' }} mr-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-500">Laba Bersih (Bulan Ini)</div>
                            <div class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($financials['profit'], 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Cash Flow Chart -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Arus Kas (6 Bulan Terakhir)</h4>
                    <div class="relative h-64 w-full">
                        <canvas id="cashflowChart"></canvas>
                    </div>
                </div>

                <!-- Occupancy Chart & Laba Tren -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 flex flex-col items-center justify-center">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 w-full text-left">Occupancy Rate</h4>
                    <div class="relative h-48 w-full max-w-[250px] flex justify-center items-center">
                        <canvas id="occupancyChart"></canvas>
                    </div>
                    <div class="mt-4 text-center">
                        <span class="text-3xl font-extrabold text-indigo-600">{{ $stats['occupancyRate'] }}%</span>
                        <p class="text-sm text-gray-500">Kamar Terisi</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 lg:col-span-2">
                    <h4 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">Tren Laba Bersih</h4>
                    <div class="relative h-64 w-full">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart Configuration Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartLabels = {!! json_encode($chartData['labels']) !!};
            const incomes = {!! json_encode($chartData['incomes']) !!};
            const expenses = {!! json_encode($chartData['expenses']) !!};
            const profits = {!! json_encode($chartData['profits']) !!};

            // 1. Cashflow Bar Chart (Income vs Expense)
            const ctxCashflow = document.getElementById('cashflowChart').getContext('2d');
            new Chart(ctxCashflow, {
                type: 'bar',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Pendapatan',
                            data: incomes,
                            backgroundColor: 'rgba(34, 197, 94, 0.7)', // Green-500
                            borderColor: 'rgb(34, 197, 94)',
                            borderWidth: 1,
                            borderRadius: 4
                        },
                        {
                            label: 'Pengeluaran',
                            data: expenses,
                            backgroundColor: 'rgba(239, 68, 68, 0.7)', // Red-500
                            borderColor: 'rgb(239, 68, 68)',
                            borderWidth: 1,
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value/1000).toLocaleString() + 'k';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': Rp ' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });

            // 2. Profit Line Chart
            const ctxProfit = document.getElementById('profitChart').getContext('2d');
            new Chart(ctxProfit, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Laba Bersih',
                        data: profits,
                        borderColor: 'rgb(99, 102, 241)', // Indigo-500
                        backgroundColor: 'rgba(99, 102, 241, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: 'rgb(99, 102, 241)',
                        pointRadius: 4,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value/1000).toLocaleString() + 'k';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Laba: Rp ' + context.parsed.y.toLocaleString();
                                }
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
                    labels: ['Terisi', 'Kosong', 'Perbaikan/Lainnya'],
                    datasets: [{
                        data: [occOccupied, occAvailable, occMaintenance],
                        backgroundColor: [
                            'rgb(239, 68, 68)', // Red
                            'rgb(34, 197, 94)', // Green
                            'rgb(156, 163, 175)' // Gray
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        });
    </script>

</x-app-layout>
