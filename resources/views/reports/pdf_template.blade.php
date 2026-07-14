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

    <div class="header">
        <h1>{{ $data['title'] ?? 'Laporan' }}</h1>
        <p>Periode: {{ $data['dateLabel'] ?? '-' }}</p>
    </div>

    <!-- Include the same table partial used in web view -->
    @include($viewName, ['data' => $data])

    <div class="footer">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} oleh Sistem Manajemen Kost
    </div>

</body>
</html>
