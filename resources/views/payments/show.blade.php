<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kuitansi Pembayaran #PAY-') . $payment->id }}
            </h2>
            <div class="space-x-2">
                <x-ui.button variant="secondary" href="{{ route('payments.index') }}">Kembali</x-ui.button>
                <x-ui.button onclick="window.print()">Print Kuitansi</x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        
        <!-- Kertas Kuitansi -->
        <div class="bg-white dark:bg-gray-800 border border-dashed border-gray-300 dark:border-gray-700 shadow-sm rounded-2xl p-6 sm:p-8 print:border-none print:shadow-none print:p-0 print:bg-white print:text-black">
            
            <div class="flex justify-between items-start mb-8 border-b-2 border-gray-100 dark:border-gray-700/60 pb-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white print:text-gray-900 tracking-wider">KUITANSI PEMBAYARAN</h1>
                    <p class="text-gray-400 text-xs mt-1 font-mono">No. Ref: #PAY-{{ $payment->id }}</p>
                </div>
                <div class="text-right">
                    <!-- Stamp/Badge Status -->
                    @if($payment->status->value === 'verified')
                        <div class="inline-block border-2 border-emerald-500 text-emerald-600 dark:text-emerald-400 px-3 py-1.5 rounded-xl font-black text-xs sm:text-sm uppercase tracking-widest bg-emerald-50/50 dark:bg-emerald-950/40">
                            LUNAS / VERIFIED
                        </div>
                    @elseif($payment->status->value === 'pending')
                        <div class="inline-block border-2 border-amber-500 text-amber-600 dark:text-amber-400 px-3 py-1.5 rounded-xl font-bold text-xs sm:text-sm uppercase tracking-widest bg-amber-50/50 dark:bg-amber-950/40">
                            MENUNGGU VERIFIKASI
                        </div>
                    @elseif($payment->status->value === 'rejected')
                        <div class="inline-block border-2 border-rose-500 text-rose-600 dark:text-rose-400 px-3 py-1.5 rounded-xl font-black text-xs sm:text-sm uppercase tracking-widest bg-rose-50/50 dark:bg-rose-950/40">
                            DITOLAK
                        </div>
                    @endif
                </div>
            </div>

            <table class="w-full text-left mb-6 text-xs text-gray-800 dark:text-gray-200">
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700/60">
                    <tr>
                        <td class="py-3 font-bold uppercase tracking-wider text-gray-400 w-1/3">Sudah terima dari</td>
                        <td class="py-3 font-bold text-base text-gray-900 dark:text-white">{{ $payment->tenant->name ?? 'Dihapus' }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 font-bold uppercase tracking-wider text-gray-400">Uang sejumlah</td>
                        <td class="py-3 font-black text-xl text-indigo-600 dark:text-indigo-400 font-mono">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="py-3 font-bold uppercase tracking-wider text-gray-400">Untuk pembayaran</td>
                        <td class="py-3">
                            <strong class="text-gray-900 dark:text-white">Tagihan INV-{{ $payment->invoice_id }}</strong><br>
                            <span class="text-gray-500 dark:text-gray-400">Sewa Kamar {{ $payment->invoice->room->room_number ?? 'Dihapus' }} (Periode {{ $payment->invoice->month }}/{{ $payment->invoice->year }})</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 font-bold uppercase tracking-wider text-gray-400">Metode Bayar</td>
                        <td class="py-3 font-semibold">{{ $payment->paymentMethod->name }}</td>
                    </tr>
                    @if($payment->notes)
                        <tr>
                            <td class="py-3 font-bold uppercase tracking-wider text-gray-400">Catatan</td>
                            <td class="py-3 italic text-gray-600 dark:text-gray-300">"{{ $payment->notes }}"</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="flex justify-between items-end mt-10 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                <div class="text-[11px] text-gray-400 space-y-0.5">
                    @if($payment->proof_path)
                        <div>* Dilengkapi dengan bukti transfer digital.</div>
                    @endif
                    @if($payment->status->value === 'verified')
                        <div>* Diverifikasi oleh: <strong class="text-gray-700 dark:text-gray-300">{{ $payment->verifier->name ?? 'Sistem' }}</strong></div>
                    @endif
                </div>
                <div class="text-center w-44">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-10">Tanggal, {{ $payment->payment_date->format('d M Y') }}</p>
                    <hr class="border-gray-300 dark:border-gray-600 mb-1">
                    <p class="text-[11px] font-bold uppercase text-gray-400">{{ config('app.name', 'Pengelola Kost') }}</p>
                </div>
            </div>

        </div>

        <!-- Action Area for Owner (Not Printed) -->
        @can('verify', $payment)
            @if($payment->status->value === 'pending')
            <div class="bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 rounded-2xl p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 print:hidden">
                <p class="text-amber-800 dark:text-amber-200 text-xs">Pembayaran ini masih pending. Silakan cek bukti transfer dan proses verifikasi.</p>
                <x-ui.button variant="warning" size="sm" href="{{ route('payments.verify', $payment) }}">Lanjut ke Verifikasi</x-ui.button>
            </div>
            @endif
        @endcan

    </div>
</x-app-layout>
