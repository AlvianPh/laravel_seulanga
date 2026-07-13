<table class="report-table w-full text-sm text-left">
    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
        <tr>
            <th>No</th>
            <th>Tanggal Pembayaran</th>
            <th>No Tagihan</th>
            <th>Penghuni</th>
            <th>Kamar</th>
            <th class="text-right">Nominal (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @forelse($data['records'] as $index => $row)
            @php $total += $row->amount; @endphp
            <tr class="border-b dark:border-gray-600">
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->payment_date->format('d/m/Y') }}</td>
                <td>INV-{{ $row->invoice_id }}</td>
                <td>{{ $row->tenant->name ?? '-' }}</td>
                <td>{{ $row->invoice->room->room_number ?? '-' }}</td>
                <td class="text-right">{{ number_format($row->amount, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center py-4 text-gray-500">Tidak ada data pendapatan pada periode ini.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <td colspan="5" class="text-right py-2 px-2">TOTAL PENDAPATAN</td>
            <td class="text-right text-green py-2 px-2">Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
