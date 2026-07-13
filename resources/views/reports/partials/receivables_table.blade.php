<table class="report-table w-full text-sm text-left">
    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
        <tr>
            <th>No</th>
            <th>No Tagihan</th>
            <th>Penghuni</th>
            <th>Kamar</th>
            <th>Jatuh Tempo</th>
            <th>Status</th>
            <th class="text-right">Nilai Piutang (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @php $total = 0; @endphp
        @forelse($data['records'] as $index => $row)
            @php $total += $row->total_amount; @endphp
            <tr class="border-b dark:border-gray-600">
                <td>{{ $index + 1 }}</td>
                <td>INV-{{ $row->id }}</td>
                <td>{{ $row->tenant->name ?? '-' }}</td>
                <td>{{ $row->room->room_number ?? '-' }}</td>
                <td>{{ $row->due_date->format('d/m/Y') }}</td>
                <td class="text-center">
                    @if($row->status->value === 'overdue')
                        <span style="color: red; font-weight: bold;">Overdue</span>
                    @else
                        <span style="color: orange; font-weight: bold;">Pending</span>
                    @endif
                </td>
                <td class="text-right">{{ number_format($row->total_amount, 0, ',', '.') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada piutang tertunggak saat ini.</td>
            </tr>
        @endforelse
    </tbody>
    <tfoot>
        <tr class="font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <td colspan="6" class="text-right py-2 px-2">TOTAL PIUTANG</td>
            <td class="text-right text-red py-2 px-2">Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    </tfoot>
</table>
