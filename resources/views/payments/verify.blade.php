<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Verifikasi Pembayaran #PAY-') . $payment->id }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Panel Data -->
            <x-ui.card>
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Verifikasi Pembayaran #PAY-{{ $payment->id }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Cocokkan data transfer rekening dengan bukti bayar penghuni</p>
                </div>
                
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 text-xs">
                    <div>
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Penghuni</dt>
                        <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $payment->tenant->name ?? 'Dihapus' }}</dd>
                    </div>
                    <div>
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Tanggal Bayar</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $payment->payment_date->format('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Metode Bayar</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $payment->paymentMethod->name }}</dd>
                    </div>
                    <div>
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Nominal Transfer</dt>
                        <dd class="mt-1 font-mono font-bold text-indigo-600 dark:text-indigo-400 text-base">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-bold uppercase tracking-wider text-gray-400 mb-1.5">Terkait Tagihan</dt>
                        <dd class="p-3 bg-gray-50/80 dark:bg-gray-800/60 rounded-xl border border-gray-100 dark:border-gray-700/60">
                            <div class="font-bold text-gray-900 dark:text-white">INV-{{ $payment->invoice_id }} (Bulan {{ $payment->invoice->month }}/{{ $payment->invoice->year }})</div>
                            <div class="text-gray-500 dark:text-gray-400 mt-1">Total Tagihan Seharusnya: <strong class="text-gray-800 dark:text-gray-200">Rp {{ number_format($payment->invoice->total_amount, 0, ',', '.') }}</strong></div>
                        </dd>
                    </div>
                </dl>

                <!-- Form Verifikasi -->
                <form method="POST" action="{{ route('payments.process-verification', $payment) }}" class="mt-6 border-t border-gray-100 dark:border-gray-700/70 pt-6">
                    @csrf
                    <div class="mb-4">
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Catatan Owner (Wajib jika DITOLAK)
                        </label>
                        <x-ui.textarea name="notes" id="notes" rows="2" class="text-sm" placeholder="Misal: Bukti transfer tidak valid, uang belum masuk ke mutasi..."></x-ui.textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <x-ui.button type="submit" name="action" value="verify" variant="primary" class="w-full">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Terima Pembayaran
                        </x-ui.button>
                        <x-ui.button type="submit" name="action" value="reject" variant="danger" class="w-full" onclick="return confirm('Yakin ingin menolak pembayaran ini? Tagihan akan tetap berstatus belum dibayar.')">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            Tolak Pembayaran
                        </x-ui.button>
                    </div>
                </form>

            </x-ui.card>

            <!-- Panel Bukti -->
            <x-ui.card>
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Bukti Transaksi Digital</h4>
                
                @if($payment->proof_path)
                    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 p-2 flex justify-center overflow-hidden">
                        <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Bukti Pembayaran" class="max-w-full h-auto max-h-96 object-contain rounded-xl">
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 text-xs font-semibold hover:underline">
                            <span>Buka Gambar Resolusi Penuh</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                @else
                    <div class="h-56 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl flex flex-col items-center justify-center text-gray-400 text-xs">
                        <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <span>Tidak ada lampiran bukti digital (misal dibayar Tunai).</span>
                    </div>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
