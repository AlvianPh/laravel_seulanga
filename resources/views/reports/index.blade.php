<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Keuangan & Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <h3 class="text-lg font-bold mb-4">Pilih Jenis Laporan & Filter Waktu</h3>

                <!-- Tampilkan error dari validasi jika ada -->
                @if ($errors->any())
                    <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- x-data AlpineJS untuk hide/show Custom Date fields -->
                <form method="POST" action="{{ route('reports.generate') }}" x-data="{ filterType: 'monthly' }">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        
                        <!-- Jenis Laporan -->
                        <div>
                            <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Laporan</label>
                            <select name="type" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <option value="income">1. Laporan Pendapatan (Pembayaran)</option>
                                <option value="expense">2. Laporan Pengeluaran</option>
                                <option value="cashflow">3. Laporan Cash Flow (Arus Kas)</option>
                                <option value="occupancy">4. Laporan Keterisian Kamar (Occupancy)</option>
                                <option value="receivables">5. Laporan Piutang Tagihan</option>
                                <option value="profit_loss">6. Laporan Laba Rugi</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Laporan Piutang & Occupancy mungkin tidak selalu terpaku pada rentang waktu di bawah.</p>
                        </div>

                        <!-- Rentang Waktu (Filter) -->
                        <div>
                            <label class="block font-medium text-gray-700 dark:text-gray-300 mb-1">Periode Waktu</label>
                            <select name="filter" x-model="filterType" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <option value="daily">Hari Ini</option>
                                <option value="weekly">Minggu Ini</option>
                                <option value="monthly">Bulan Ini</option>
                                <option value="yearly">Tahun Ini</option>
                                <option value="custom">-- Rentang Waktu Kustom --</option>
                            </select>
                        </div>

                    </div>

                    <!-- Input Custom Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg" x-show="filterType === 'custom'" style="display: none;">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Mulai</label>
                            <input type="date" name="start_date" :required="filterType === 'custom'" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Akhir</label>
                            <input type="date" name="end_date" :required="filterType === 'custom'" class="w-full border-gray-300 rounded-md shadow-sm dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        </div>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6 mt-4 flex items-center justify-between">
                        
                        <div class="text-sm text-gray-500">
                            Data akan disajikan dalam bentuk tabel terlebih dahulu.
                        </div>

                        <div class="space-x-2">
                            <button type="submit" name="action" value="view" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700 shadow flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                Lihat Laporan
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
