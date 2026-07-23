<table>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 16px;">{{ $setting->kost_name }}</td>
    </tr>
    @if($setting->kost_address)
    <tr>
        <td colspan="5">{{ $setting->kost_address }}</td>
    </tr>
    @endif
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td colspan="5" style="font-weight: bold; font-size: 14px;">{{ $data['title'] ?? 'Laporan' }}</td>
    </tr>
    <tr>
        <td colspan="5">Periode: {{ $data['dateLabel'] ?? '-' }}</td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
</table>

<!-- Include original table -->
@include($viewName, ['data' => $data])
