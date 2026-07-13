<div class="mb-4">
    <p>Data Status Kamar saat ini:</p>
</div>
<table class="report-table w-full text-sm text-left">
    <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
        <tr>
            <th>Status Kamar</th>
            <th class="text-center">Jumlah Unit</th>
            <th class="text-right">Persentase</th>
        </tr>
    </thead>
    <tbody>
        <tr class="border-b dark:border-gray-600">
            <td class="font-bold text-red-600">Terisi (Occupied)</td>
            <td class="text-center">{{ $data['stats']['occupied'] }}</td>
            <td class="text-right">{{ $data['stats']['total'] > 0 ? round(($data['stats']['occupied'] / $data['stats']['total']) * 100, 1) : 0 }} %</td>
        </tr>
        <tr class="border-b dark:border-gray-600">
            <td class="font-bold text-green-600">Kosong (Available)</td>
            <td class="text-center">{{ $data['stats']['available'] }}</td>
            <td class="text-right">{{ $data['stats']['total'] > 0 ? round(($data['stats']['available'] / $data['stats']['total']) * 100, 1) : 0 }} %</td>
        </tr>
        <tr class="border-b dark:border-gray-600">
            <td class="font-bold text-yellow-600">Perbaikan (Maintenance)</td>
            <td class="text-center">{{ $data['stats']['maintenance'] }}</td>
            <td class="text-right">{{ $data['stats']['total'] > 0 ? round(($data['stats']['maintenance'] / $data['stats']['total']) * 100, 1) : 0 }} %</td>
        </tr>
    </tbody>
    <tfoot>
        <tr class="font-bold bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100">
            <td class="text-right py-2 px-2">TOTAL KAPASITAS</td>
            <td class="text-center py-2 px-2">{{ $data['stats']['total'] }} Unit</td>
            <td class="text-right py-2 px-2">100 %</td>
        </tr>
        <tr class="font-bold">
            <td class="text-right py-2 px-2">OCCUPANCY RATE</td>
            <td colspan="2" class="text-center py-2 px-2 bg-indigo-100 text-indigo-700 text-lg">
                {{ $data['stats']['rate'] }} %
            </td>
        </tr>
    </tfoot>
</table>
