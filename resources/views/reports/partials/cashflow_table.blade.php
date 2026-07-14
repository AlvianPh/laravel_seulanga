<table class="report-table w-full text-sm text-left">
    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
        <tr>
            <th>No</th>
            <th>Tanggal Transaksi</th>
            <th>Jenis</th>
            <th>Keterangan</th>
            <th class="text-right text-green">Uang Masuk (Rp)</th>
            <th class="text-right text-red">Uang Keluar (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php 
            $totalIn = 0; 
            $totalOut = 0;
        @endphp
        @forelse($data['records'] as $index => $row)
            @php 
                if ($row->type === 'income') $totalIn += $row->amount; 
                else $totalOut += $row->amount; 
            @endphp
            <tr class="border-b dark:border-gray-600">
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($row->date)->format('d/m/Y') }}</td>
                <td>{{ $row->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}</td>
                <td>{{ $row->description }}</td>
                <td class="text-right text-green">{{ $row->type === 'income' ? number_format($row->amount, 0, ',', '.') : '-' }}</td>
                <td class="text-right text-red">{{ $row->type === 'expense' ? number_format($row->amount, 0, ',', '.') : '-' }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data arus kas pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <td colspan="4" class="text-right py-2 px-2">TOTAL</td>
            <td class="text-right text-green py-2 px-2">Rp {{ number_format($totalIn, 0, ',', '.') }}</td>
            <td class="text-right text-red py-2 px-2">Rp {{ number_format($totalOut, 0, ',', '.') }}</td>
        </tr>
        <tr class="font-bold text-gray-900 dark:text-white">
            <td colspan="4" class="text-right py-2 px-2">SALDO BERSIH (NET)</td>
            <td colspan="2" class="text-center py-2 px-2 {{ ($totalIn - $totalOut) >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                Rp {{ number_format($totalIn - $totalOut, 0, ',', '.') }}
            </td>
        </tr>
    </tfoot>
</table>
