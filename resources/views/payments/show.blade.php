<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Kuitansi Pembayaran #PAY-') . $payment->id }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Kembali</a>
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Print Kuitansi</button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Kertas Kuitansi -->
            <div class="bg-white border-2 border-dashed border-gray-300 shadow-sm sm:rounded-lg p-8 print:border-none print:shadow-none print:p-0">
                
                <div class="flex justify-between items-start mb-8 border-b-2 border-gray-100 pb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 tracking-wider">KUITANSI</h1>
                        <p class="text-gray-500 text-sm mt-1 font-mono">No. Ref: PAY-{{ $payment->id }}</p>
                    </div>
                    <div class="text-right">
                        <!-- Stamp/Badge Status -->
                        @if($payment->status->value === 'verified')
                            <div class="inline-block border-4 border-green-500 text-green-600 px-4 py-2 rounded-lg transform -rotate-6 font-bold text-lg opacity-80 uppercase tracking-widest">
                                LUNAS / VERIFIED
                            </div>
                        @elseif($payment->status->value === 'pending')
                            <div class="inline-block border-4 border-yellow-500 text-yellow-600 px-4 py-2 rounded-lg font-bold text-lg opacity-80 uppercase tracking-widest">
                                MENUNGGU VERIFIKASI
                            </div>
                        @elseif($payment->status->value === 'rejected')
                            <div class="inline-block border-4 border-red-500 text-red-600 px-4 py-2 rounded-lg transform rotate-6 font-bold text-lg opacity-80 uppercase tracking-widest">
                                DITOLAK
                            </div>
                        @endif
                    </div>
                </div>

                <table class="w-full text-left mb-6 text-gray-800">
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-3 text-sm text-gray-500 w-1/3">Sudah terima dari</td>
                            <td class="py-3 font-bold text-lg">{{ $payment->tenant->name ?? 'Dihapus' }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-sm text-gray-500">Uang sejumlah</td>
                            <td class="py-3 font-bold text-2xl text-indigo-700">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3 text-sm text-gray-500">Untuk pembayaran</td>
                            <td class="py-3">
                                <strong>Tagihan INV-{{ $payment->invoice_id }}</strong><br>
                                Sewa Kamar {{ $payment->invoice->room->room_number ?? 'Dihapus' }} (Bulan {{ $payment->invoice->month }}/{{ $payment->invoice->year }})
                            </td>
                        </tr>
                        <tr>
                            <td class="py-3 text-sm text-gray-500">Metode Bayar</td>
                            <td class="py-3 font-semibold">{{ $payment->paymentMethod->name }}</td>
                        </tr>
                        @if($payment->notes)
                            <tr>
                                <td class="py-3 text-sm text-gray-500">Catatan</td>
                                <td class="py-3 italic text-gray-600">"{{ $payment->notes }}"</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <div class="flex justify-between items-end mt-12 pt-8">
                    <div class="text-sm text-gray-500">
                        @if($payment->proof_path)
                            * Dilengkapi dengan bukti transfer digital.<br>
                        @endif
                        @if($payment->status->value === 'verified')
                            * Diverifikasi oleh: <strong>{{ $payment->verifier->name ?? 'Sistem' }}</strong>
                        @endif
                    </div>
                    <div class="text-center w-48">
                        <p class="text-gray-600 mb-12">Tanggal, {{ $payment->payment_date->format('d M Y') }}</p>
                        <hr class="border-gray-400 mb-1">
                        <p class="text-sm text-gray-500 uppercase">{{ config('app.name', 'Penerima') }}</p>
                    </div>
                </div>

            </div>

            <!-- Action Area for Owner (Not Printed) -->
            @can('verify', $payment)
                @if($payment->status->value === 'pending')
                <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded p-4 flex justify-between items-center print:hidden">
                    <p class="text-yellow-800 text-sm">Pembayaran ini masih pending. Silakan cek bukti dan verifikasi.</p>
                    <a href="{{ route('payments.verify', $payment) }}" class="px-4 py-2 bg-yellow-600 text-white rounded font-bold hover:bg-yellow-700">Lanjut ke Verifikasi</a>
                </div>
                @endif
            @endcan

        </div>
    </div>
</x-app-layout>
