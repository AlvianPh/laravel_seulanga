<!DOCTYPE html>
<html>
<head>
    <title>{{ $data['title'] ?? 'Laporan' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            color: #555;
        }
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .report-table th, .report-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .report-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .text-red {
            color: red;
        }
        .text-green {
            color: green;
        }
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #888;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header" style="border:none;">
        @php
            $setting = \App\Models\Setting::getInstance();
            $logoPath = $setting->kost_logo ? public_path('storage/' . $setting->kost_logo) : null;
            // Fallback for dompdf to read file if symlink issue
            if ($logoPath && !file_exists($logoPath)) {
                $logoPath = storage_path('app/public/' . $setting->kost_logo);
            }
        @endphp
        <table style="width: 100%; border-bottom: 2px solid #333; margin-bottom: 20px; padding-bottom: 10px; border-collapse: collapse;">
            <tr>
                @if($logoPath && file_exists($logoPath))
                <td style="width: 80px; text-align: left; vertical-align: middle; border: none;">
                    <img src="{{ $logoPath }}" style="max-height: 60px; max-width: 60px;">
                </td>
                @endif
                <td style="text-align: {{ ($logoPath && file_exists($logoPath)) ? 'left' : 'center' }}; vertical-align: middle; border: none;">
                    <h1 style="margin: 0; font-size: 24px;">{{ $setting->kost_name }}</h1>
                    @if($setting->kost_address)
                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #555;">{{ $setting->kost_address }}</p>
                    @endif
                </td>
            </tr>
        </table>
        <h2 style="margin: 0; font-size: 18px; text-align: center;">{{ $data['title'] ?? 'Laporan' }}</h2>
        <p style="text-align: center; font-size: 14px; margin-top: 5px; color: #555;">Periode: {{ $data['dateLabel'] ?? '-' }}</p>
    </div>

    <!-- Include the same table partial used in web view -->
    @include($viewName, ['data' => $data])

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh Sistem Manajemen Kost
    </div>

</body>
</html>
