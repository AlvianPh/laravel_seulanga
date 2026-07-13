<table class="report-table w-full text-sm text-left">
    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
        <tr>
            <th>No</th>
            <th>Tanggal Pengeluaran</th>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th>Dicatat Oleh</th>
            <th class="text-right">Nominal (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @forelse($data['records'] as $index => $row)
            @php $total += $row->amount; @endphp
            <tr class="border-b dark:border-gray-600">
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->expense_date->format('d/m/Y') }}</td>
                <td>{{ $row->category->label() }}</td>
                <td>{{ $row->description }}</td>
                <td>{{ $row->creator->name ?? '-' }}</td>
                <td class="text-right">{{ number_format($row->amount, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data pengeluaran pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <td colspan="5" class="text-right py-2 px-2">TOTAL PENGELUARAN</td>
            <td class="text-right text-red py-2 px-2">Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
