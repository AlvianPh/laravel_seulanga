<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Verifikasi Pembayaran #PAY-') . $payment->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Panel Data -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Informasi Pembayaran</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 text-sm">
                        <div class="sm:col-span-1">
                            <dt class="font-medium text-gray-500">Penghuni</dt>
                            <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $payment->tenant->name ?? 'Dihapus' }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="font-medium text-gray-500">Tanggal Bayar</dt>
                            <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $payment->payment_date->format('d F Y') }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="font-medium text-gray-500">Metode</dt>
                            <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $payment->method->label() }}</dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="font-medium text-gray-500">Nominal yang Diakui User</dt>
                            <dd class="mt-1 font-bold text-indigo-600 text-lg">Rp {{ number_format($payment->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-500">Terkait Tagihan</dt>
                            <dd class="mt-1 p-3 bg-gray-50 dark:bg-gray-700 rounded border dark:border-gray-600">
                                <div class="font-bold">INV-{{ $payment->invoice_id }} (Bulan {{ $payment->invoice->month }}/{{ $payment->invoice->year }})</div>
                                <div class="text-gray-600 dark:text-gray-300 mt-1">Total Tagihan Seharusnya: <strong>Rp {{ number_format($payment->invoice->total_amount, 0, ',', '.') }}</strong></div>
                            </dd>
                        </div>
                    </dl>

                    <!-- Form Verifikasi -->
                    <form method="POST" action="{{ route('payments.process-verification', $payment) }}" class="mt-8 border-t pt-6">
                        @csrf
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Catatan Owner (Wajib diisi jika DITOLAK)
                            </label>
                            <textarea name="notes" id="notes" rows="2" class="w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Misal: Bukti transfer buram, uang belum masuk..."></textarea>
                            @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" name="action" value="verify" class="flex-1 bg-green-600 text-white font-bold py-3 rounded-lg hover:bg-green-700 shadow flex justify-center items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Terima Pembayaran
                            </button>
                            <button type="submit" name="action" value="reject" class="flex-1 bg-red-600 text-white font-bold py-3 rounded-lg hover:bg-red-700 shadow flex justify-center items-center" onclick="return confirm('Yakin ingin menolak pembayaran ini? Tagihan akan tetap dianggap belum dibayar.')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                Tolak Pembayaran
                            </button>
                        </div>
                    </form>

                </div>

                <!-- Panel Bukti -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Bukti Transaksi</h3>
                    
                    @if($payment->proof_path)
                        <div class="border rounded bg-gray-50 dark:bg-gray-900 p-2 flex justify-center">
                            <img src="{{ asset('storage/' . $payment->proof_path) }}" alt="Bukti Pembayaran" class="max-w-full h-auto max-h-96 object-contain rounded">
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ asset('storage/' . $payment->proof_path) }}" target="_blank" class="text-indigo-600 text-sm hover:underline">
                                Lihat Gambar Resolusi Penuh &nearr;
                            </a>
                        </div>
                    @else
                        <div class="h-48 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex items-center justify-center text-gray-500">
                            Tidak ada file bukti (Biasanya dibayar tunai).
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
