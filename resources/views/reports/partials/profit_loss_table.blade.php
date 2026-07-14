<div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="p-4 border rounded bg-green-50 dark:bg-gray-700">
        <h4 class="font-bold text-green-700 dark:text-green-400">Total Pendapatan (Gross)</h4>
        <p class="text-2xl font-bold">Rp {{ number_format($data['report']['total_income'], 0, ',', '.') }}</p>
    </div>
    <div class="p-4 border rounded bg-red-50 dark:bg-gray-700">
        <h4 class="font-bold text-red-700 dark:text-red-400">Total Beban/Pengeluaran</h4>
        <p class="text-2xl font-bold">Rp {{ number_format($data['report']['total_expense'], 0, ',', '.') }}</p>
    </div>
</div>

<h3 class="font-bold mb-2 text-lg">Rincian Beban Operasional:</h3>
<table class="report-table w-full text-sm text-left mb-6">
    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
        <tr>
            <th>Kategori Pengeluaran</th>
            <th class="text-right">Total (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($data['report']['expense_breakdown'] as $exp)
            <tr class="border-b dark:border-gray-600">
                <td>{{ $exp['label'] }}</td>
                <td class="text-right">{{ number_format($exp['total'], 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="text-center py-4 text-gray-500">Tidak ada pengeluaran pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="p-4 rounded-lg {{ $data['report']['net_profit'] >= 0 ? 'bg-indigo-100 text-indigo-800' : 'bg-red-100 text-red-800' }} flex justify-between items-center text-xl font-bold">
    <span>LABA BERSIH (NET PROFIT)</span>
    <span>Rp {{ number_format($data['report']['net_profit'], 0, ',', '.') }}</span>
</div>
