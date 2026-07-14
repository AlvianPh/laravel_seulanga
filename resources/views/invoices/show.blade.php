<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Invoice #INV-') . $invoice->id }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('invoices.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Kembali</a>
                <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Print</button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-8 print:p-0 print:shadow-none print:bg-white print:text-black">

                <!-- Header Invoice -->
                <div class="flex justify-between items-start border-b pb-6 mb-6 border-gray-200 dark:border-gray-700">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 print:text-gray-900">INVOICE</h1>
                        <p class="text-gray-500 text-sm mt-1">Periode: {{ $invoice->month }} / {{ $invoice->year }}</p>
                        <p class="text-gray-500 text-sm font-mono mt-1">#INV-{{ $invoice->id }}</p>
                    </div>
                    <div class="text-right">
                        <div class="px-4 py-1 inline-block rounded text-sm font-bold uppercase tracking-wider
                            @if($invoice->status->value === 'paid') bg-green-100 text-green-700
                            @elseif($invoice->status->value === 'pending') bg-yellow-100 text-yellow-800
                            @elseif($invoice->status->value === 'overdue') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif
                            print:border print:border-gray-400 print:bg-white print:text-black
                        ">
                            {{ $invoice->status->label() }}
                        </div>
                        <p class="text-gray-500 text-sm mt-2">
                            Jatuh Tempo: <br>
                            <strong class="{{ $invoice->status->value === 'overdue' ? 'text-red-600' : 'text-gray-800 dark:text-gray-200' }}">
                                {{ $invoice->due_date->format('d M Y') }}
                            </strong>
                        </p>
                    </div>
                </div>

                <!-- Info Pihak -->
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">Ditagihkan Kepada:</h4>
                        <p class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $invoice->tenant->name ?? 'Dihapus' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">No. HP: {{ $invoice->tenant->phone ?? '-' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Kamar: {{ $invoice->room->room_number ?? 'Dihapus' }}</p>
                    </div>
                    <div class="text-right">
                        <h4 class="text-xs text-gray-500 uppercase tracking-wide mb-2">Penerima (Manajemen):</h4>
                        <p class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ config('app.name', 'Kost Management') }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Sistem Administrasi Kost</p>
                    </div>
                </div>

                <!-- Rincian Biaya -->
                <table class="w-full text-left mb-8">
                    <thead class="border-b-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="py-2 text-sm font-semibold text-gray-700 dark:text-gray-300">Deskripsi Tagihan</th>
                            <th class="py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm text-gray-800 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr>
                            <td class="py-3">Sewa Kamar Pokok</td>
                            <td class="py-3 text-right">{{ number_format($invoice->rent_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->electricity_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Listrik Tambahan</td>
                                <td class="py-3 text-right">{{ number_format($invoice->electricity_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->water_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Air Tambahan</td>
                                <td class="py-3 text-right">{{ number_format($invoice->water_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->internet_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Internet / WiFi</td>
                                <td class="py-3 text-right">{{ number_format($invoice->internet_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->penalty_fee > 0)
                            <tr>
                                <td class="py-3 text-red-600 font-medium">Denda Keterlambatan / Kerusakan</td>
                                <td class="py-3 text-right text-red-600 font-medium">{{ number_format($invoice->penalty_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->other_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Lain-lain</td>
                                <td class="py-3 text-right">{{ number_format($invoice->other_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="border-t-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <td class="py-4 text-right font-bold text-gray-900 dark:text-gray-100 text-lg">TOTAL TAGIHAN</td>
                            <td class="py-4 text-right font-bold text-indigo-600 text-xl">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Action Button (Tidak tampil saat di-print) -->
                <div class="mt-8 pt-4 border-t border-gray-100 dark:border-gray-800 flex justify-between items-center print:hidden">
                    <p class="text-sm text-gray-500">
                        Pastikan untuk selalu mengecek komponen tambahan sebelum menagih.
                    </p>
                    <a href="{{ route('invoices.edit', $invoice) }}" class="px-6 py-2 bg-yellow-500 text-white font-semibold rounded hover:bg-yellow-600">
                        Edit Komponen Biaya
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
