<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuitansi Pembayaran — {{ $payment->receiptNumber() }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .receipt-container {
                box-shadow: none !important;
                border: 1px solid #e2e8f0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 p-4 sm:p-8 min-h-screen flex flex-col items-center justify-center">
    <!-- Top Action Bar (hidden on print) -->
    <div class="no-print max-w-2xl w-full mb-4 flex items-center justify-between">
        <a href="{{ route('portal.payments.show', $payment) }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            &larr; Kembali
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Kuitansi
        </button>
    </div>

    <!-- Receipt Container -->
    <div class="receipt-container max-w-2xl w-full bg-white rounded-2xl shadow-lg border border-slate-200 p-8 sm:p-10 relative overflow-hidden">
        <!-- Watermark -->
        <div class="absolute right-6 top-24 opacity-5 pointer-events-none select-none text-8xl font-black text-emerald-900">
            PAID
        </div>

        <!-- Header -->
        <div class="flex items-start justify-between border-b-2 border-slate-900 pb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    {{ $setting->kost_name ?? 'Kost Seulanga' }}
                </h1>
                <p class="text-xs text-slate-500 mt-1 max-w-sm">
                    {{ $setting->kost_address ?? 'Sistem Manajemen Hunian Kost Mandiri' }}
                </p>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-black uppercase tracking-wider">
                    LUNAS / VERIFIED
                </span>
                <p class="text-xs font-mono font-bold text-slate-700 mt-2">
                    {{ $payment->receiptNumber() }}
                </p>
            </div>
        </div>

        <!-- Receipt Title -->
        <div class="py-6 text-center">
            <h2 class="text-lg sm:text-xl font-extrabold uppercase tracking-widest text-slate-900">
                Kuitansi Pembayaran
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Bukti sah tanda penerimaan pembayaran sewa kamar
            </p>
        </div>

        <!-- Details Grid -->
        <div class="space-y-4 text-xs sm:text-sm">
            <div class="flex flex-col sm:flex-row sm:items-center py-2.5 border-b border-slate-100">
                <span class="w-44 text-slate-500 font-medium">Telah Diterima Dari:</span>
                <span class="font-bold text-slate-900 uppercase">{{ $payment->tenant?->name ?? '-' }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center py-2.5 border-b border-slate-100">
                <span class="w-44 text-slate-500 font-medium">Nomor Identitas (NIK):</span>
                <span class="font-mono text-slate-900">{{ $payment->tenant?->nik ?? '-' }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center py-2.5 border-b border-slate-100">
                <span class="w-44 text-slate-500 font-medium">Untuk Pembayaran:</span>
                <span class="font-semibold text-slate-900">
                    Tagihan Sewa Bulan {{ \Carbon\Carbon::createFromDate($payment->invoice?->year, $payment->invoice?->month, 1)->translatedFormat('F Y') }}
                    • Kamar {{ $payment->invoice?->room?->room_number ?? '-' }}
                </span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center py-2.5 border-b border-slate-100">
                <span class="w-44 text-slate-500 font-medium">Metode Pembayaran:</span>
                <span class="font-semibold text-slate-900">{{ $payment->paymentMethod?->name ?? 'Transfer Bank' }}</span>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center py-2.5 border-b border-slate-100">
                <span class="w-44 text-slate-500 font-medium">Tanggal Transaksi:</span>
                <span class="text-slate-900">{{ $payment->payment_date->translatedFormat('d F Y') }}</span>
            </div>

            <!-- Amount Box -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Jumlah Pembayaran:</span>
                <span class="text-xl sm:text-2xl font-black text-emerald-700">
                    Rp {{ number_format($payment->amount, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Footer & Signatures -->
        <div class="mt-10 pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-start sm:items-end justify-between gap-6 text-xs text-slate-500">
            <div>
                <p class="text-[11px] leading-relaxed">
                    Dokumen kuitansi ini diterbitkan secara otomatis oleh sistem <strong>{{ config('app.name', 'Seulanga') }}</strong>.<br>
                    Status pembayaran telah diverifikasi dan dicatat pada buku kas.
                </p>
            </div>
            <div class="text-left sm:text-right shrink-0">
                <p class="text-slate-400">Verifikator Pengelola,</p>
                <div class="h-12 flex items-center justify-start sm:justify-end">
                    <span class="inline-block px-2.5 py-1 rounded bg-emerald-50 text-emerald-800 font-mono font-bold text-[11px] border border-emerald-200">
                        [DIGITALLY VERIFIED]
                    </span>
                </div>
                <p class="font-bold text-slate-900">{{ $payment->verifier?->name ?? 'Pengelola Kost' }}</p>
                <p class="text-[10px] text-slate-400">{{ $payment->updated_at->translatedFormat('d F Y') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
