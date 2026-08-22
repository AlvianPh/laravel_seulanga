<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Laporan Keuangan & Operasional') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Laporan Keuangan & Operasional</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pilih jenis laporan dan rentang periode waktu yang ingin dianalisis</p>
            </div>

            <!-- Tampilkan error dari validasi jika ada -->
            @if ($errors->any())
                <x-ui.alert type="error" class="mb-6">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-ui.alert>
            @endif

            <!-- x-data AlpineJS untuk hide/show Custom Date fields -->
            <form method="POST" action="{{ route('reports.generate') }}" x-data="{ filterType: 'monthly' }">
                @csrf
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    
                    <!-- Jenis Laporan -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Pilih Jenis Laporan</label>
                        <x-ui.select name="type" required class="text-sm">
                            <option value="income">1. Laporan Pendapatan (Kas Masuk)</option>
                            <option value="expense">2. Laporan Pengeluaran Operasional</option>
                            <option value="cashflow">3. Laporan Arus Kas (Cash Flow)</option>
                            <option value="occupancy">4. Laporan Keterisian Kamar (Occupancy)</option>
                            <option value="receivables">5. Laporan Piutang / Tunggakan</option>
                            <option value="profit_loss">6. Laporan Laba Rugi Bersih</option>
                        </x-ui.select>
                    </div>

                    <!-- Rentang Waktu (Filter) -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Periode Waktu</label>
                        <x-ui.select name="filter" x-model="filterType" required class="text-sm">
                            <option value="daily">Hari Ini</option>
                            <option value="weekly">Minggu Ini</option>
                            <option value="monthly">Bulan Ini</option>
                            <option value="yearly">Tahun Ini</option>
                            <option value="custom">-- Rentang Tanggal Kustom --</option>
                        </x-ui.select>
                    </div>

                </div>

                <!-- Input Custom Date Range -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50/80 dark:bg-gray-900/50 rounded-2xl border border-gray-200/80 dark:border-gray-700/70" x-show="filterType === 'custom'" x-cloak>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Mulai</label>
                        <x-ui.input type="date" name="start_date" x-bind:required="filterType === 'custom'" class="text-sm" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Akhir</label>
                        <x-ui.input type="date" name="end_date" x-bind:required="filterType === 'custom'" class="text-sm" />
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700/60 pt-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Data akan disajikan dalam bentuk tabel ringkasan dan rincian transaksi siap cetak.
                    </p>

                    <x-ui.button type="submit" name="action" value="view" variant="primary" size="md">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        Tampilkan Laporan
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
