<x-portal-layout>
    <div class="space-y-6 max-w-3xl mx-auto">
        <!-- Back Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('portal.payments.index') }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Riwayat Pembayaran
            </a>
            <x-ui.badge :status="$payment->status">
                {{ $payment->status->label() }}
            </x-ui.badge>
        </div>

        <!-- Payment Detail Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Nomor Transaksi Pembayaran</span>
                    <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                        {{ $payment->receiptNumber() }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Dikirim pada: {{ $payment->created_at->translatedFormat('d F Y, H:i') }}
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Nominal Dibayar</span>
                    <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-400">Metode Pembayaran</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">
                        {{ $payment->paymentMethod?->name ?? 'Transfer Bank' }}
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-400">Tanggal Pembayaran</span>
                    <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">
                        {{ $payment->payment_date->translatedFormat('d F Y') }}
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-400">Tagihan Terkait</span>
                    @if ($payment->invoice)
                        <p class="text-sm font-bold mt-0.5">
                            <a href="{{ route('portal.invoices.show', $payment->invoice) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline">
                                Bulan {{ \Carbon\Carbon::createFromDate($payment->invoice->year, $payment->invoice->month, 1)->translatedFormat('F Y') }} &rarr;
                            </a>
                        </p>
                    @else
                        <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">-</p>
                    @endif
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-400">Status Verifikasi</span>
                    <p class="text-sm font-bold mt-0.5">
                        <x-ui.badge :status="$payment->status">
                            {{ $payment->status->label() }}
                        </x-ui.badge>
                    </p>
                </div>
            </div>

            @if ($payment->verifier)
                <div class="mt-4 p-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-bold">Pembayaran Telah Diverifikasi</p>
                        <p class="text-[11px] opacity-90 mt-0.5">Diverifikasi oleh <strong>{{ $payment->verifier->name }}</strong>.</p>
                    </div>
                </div>
            @endif

            @if ($payment->isRejected())
                <div class="mt-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-xs text-rose-900 dark:text-rose-200">
                    <p class="font-bold">Pembayaran Ditolak</p>
                    <p class="text-[11px] mt-1">{{ $payment->notes ?? 'Bukti pembayaran tidak sesuai atau tidak terbaca.' }}</p>
                    @if ($payment->invoice && !$payment->invoice->isPaid())
                        <div class="mt-3">
                            <a href="{{ route('portal.invoices.show', $payment->invoice) }}"
                               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white shadow-sm transition-colors">
                                Kirim Ulang Bukti Pembayaran &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            @endif

            @if ($payment->notes && !$payment->isRejected())
                <div class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-xs text-slate-700 dark:text-slate-300">
                    <span class="font-bold text-slate-500 dark:text-slate-400 block mb-1">Catatan:</span>
                    <p>{{ $payment->notes }}</p>
                </div>
            @endif

            <!-- Proof Preview -->
            @if ($payment->proof_path)
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Bukti Pembayaran / Transfer</h3>
                    @php
                        $ext = pathinfo($payment->proof_path, PATHINFO_EXTENSION);
                    @endphp
                    @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png']))
                        <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 max-w-sm">
                            <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Bukti Transfer" class="w-full object-cover hover:opacity-90 transition-opacity">
                            </a>
                        </div>
                    @else
                        <a href="{{ asset('storage/' . $payment->proof_path) }}"
                           target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Unduh Dokumen Bukti ({{ strtoupper($ext) }})
                        </a>
                    @endif
                </div>
            @endif

            @if ($payment->isVerified())
                <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800 flex justify-end">
                    <a href="{{ route('portal.payments.receipt', $payment) }}"
                       target="_blank"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Cetak Kuitansi Resmi
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>
