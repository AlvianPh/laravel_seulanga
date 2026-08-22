<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="font-bold text-xl text-gray-900 dark:text-white tracking-tight">
                {{ __('Tagihan #INV-') . $invoice->id }}
            </h2>
            <div class="flex items-center gap-2">
                @php
                    $phone = $invoice->tenant->phone ?? '';
                    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                    if (str_starts_with($cleanPhone, '0')) {
                        $cleanPhone = '62' . substr($cleanPhone, 1);
                    }
                    $waText = rawurlencode("Halo Kak " . ($invoice->tenant->name ?? '') . ",\n\nBerikut rincian tagihan sewa Kamar " . ($invoice->room->room_number ?? '') . " periode " . $invoice->month . "/" . $invoice->year . " sebesar Rp " . number_format($invoice->total_amount, 0, ',', '.') . ".\nJatuh tempo: " . $invoice->due_date->format('d M Y') . ".\n\nMohon konfirmasi jika sudah melakukan pembayaran. Terima kasih! 🙏");
                @endphp
                @if($cleanPhone && $invoice->status->value !== 'paid')
                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition-all">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Kirim WA</span>
                    </a>
                @endif
                <x-ui.button variant="secondary" size="sm" href="{{ route('invoices.index') }}">Kembali</x-ui.button>
                <x-ui.button variant="primary" size="sm" onclick="window.print()">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 sm:p-8 print:p-0 print:shadow-none print:bg-white print:text-black border border-gray-100 dark:border-gray-700/70">
            @php
                $setting = \App\Models\Setting::getInstance();
            @endphp

            <!-- KOP KOST -->
            <div class="flex items-center gap-4 border-b-2 border-gray-900 dark:border-gray-700 pb-4 mb-6">
                @if($setting->kost_logo)
                    <img src="{{ Storage::url($setting->kost_logo) }}" alt="Logo Kost" class="h-16 w-16 object-cover rounded-xl shadow-xs">
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white print:text-gray-900">{{ $setting->kost_name }}</h1>
                    @if($setting->kost_address)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 print:text-gray-600">{{ $setting->kost_address }}</p>
                    @endif
                </div>
            </div>

            <!-- Header Invoice -->
            <div class="flex justify-between items-start border-b pb-6 mb-6 border-gray-100 dark:border-gray-700/70">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white print:text-gray-900 tracking-tight">INVOICE SEWA</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Periode: {{ $invoice->month }} / {{ $invoice->year }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-mono mt-0.5">#INV-{{ $invoice->id }}</p>
                </div>
                <div class="text-right">
                    <x-ui.badge :status="$invoice->status" class="print:border print:border-gray-400 print:bg-white print:text-black">{{ $invoice->status->label() }}</x-ui.badge>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Jatuh Tempo: <br>
                        <strong class="{{ $invoice->status->value === 'overdue' ? 'text-rose-600 dark:text-rose-400' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ $invoice->due_date->format('d M Y') }}
                        </strong>
                    </p>
                </div>
            </div>

            <!-- Info Pihak -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8 text-xs">
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-gray-400 mb-2">Ditagihkan Kepada:</h4>
                    <p class="font-bold text-gray-900 dark:text-white text-base">{{ $invoice->tenant->name ?? 'Dihapus' }}</p>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">No. HP / WA: {{ $invoice->tenant->phone ?? '-' }}</p>
                    <p class="text-gray-600 dark:text-gray-300 mt-0.5">Unit: Kamar {{ $invoice->room->room_number ?? 'Dihapus' }}</p>
                </div>
                <div class="sm:text-right">
                    <h4 class="font-bold uppercase tracking-wider text-gray-400 mb-2">Penerima Tagihan:</h4>
                    <p class="font-bold text-gray-900 dark:text-white text-base">{{ $setting->kost_name }}</p>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">Sistem Administrasi Kost & Properti</p>
                </div>
            </div>

            <!-- Rincian Biaya -->
            <div class="overflow-x-auto mb-8">
                <table class="w-full text-left text-xs">
                    <thead class="border-b-2 border-gray-200 dark:border-gray-700">
                        <tr>
                            <th class="py-2.5 font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">Deskripsi Rincian Tagihan</th>
                            <th class="py-2.5 font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 text-right">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700/60">
                        <tr>
                            <td class="py-3 font-semibold text-gray-900 dark:text-white">Sewa Kamar Pokok (Bulan {{ $invoice->month }}/{{ $invoice->year }})</td>
                            <td class="py-3 text-right font-mono">{{ number_format($invoice->rent_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($invoice->electricity_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Listrik Tambahan / Ekstra</td>
                                <td class="py-3 text-right font-mono">{{ number_format($invoice->electricity_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->water_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Pemakaian Air</td>
                                <td class="py-3 text-right font-mono">{{ number_format($invoice->water_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->internet_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Layanan Internet / WiFi</td>
                                <td class="py-3 text-right font-mono">{{ number_format($invoice->internet_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->penalty_fee > 0)
                            <tr>
                                <td class="py-3 text-rose-600 dark:text-rose-400 font-semibold">Denda Keterlambatan / Kerusakan</td>
                                <td class="py-3 text-right text-rose-600 dark:text-rose-400 font-bold font-mono">{{ number_format($invoice->penalty_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                        @if($invoice->other_fee > 0)
                            <tr>
                                <td class="py-3">Biaya Lain-lain / Tambahan Khusus</td>
                                <td class="py-3 text-right font-mono">{{ number_format($invoice->other_fee, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot class="border-t-2 border-gray-900 dark:border-gray-600">
                        <tr>
                            <td class="py-4 text-right font-bold text-gray-900 dark:text-white text-sm uppercase tracking-wider">TOTAL TAGIHAN</td>
                            <td class="py-4 text-right font-bold text-indigo-600 dark:text-indigo-400 text-xl font-mono">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Info Rekening -->
            @if($setting->defaultBankAccount)
            <div class="mt-6 p-4 bg-gray-50/80 dark:bg-gray-700/40 rounded-2xl print:bg-white print:border print:border-gray-300 border border-gray-100 dark:border-gray-700/70">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-2">Instruksi Pembayaran Transfer Bank</h4>
                <p class="text-xs text-gray-600 dark:text-gray-300 leading-relaxed">
                    Pembayaran dapat ditransfer ke rekening bank resmi berikut:<br>
                    Bank: <strong class="text-gray-900 dark:text-white">{{ $setting->defaultBankAccount->nama_bank }}</strong><br>
                    No. Rekening: <strong class="text-base text-indigo-600 dark:text-indigo-400 font-mono tracking-wider">{{ $setting->defaultBankAccount->nomor_rekening }}</strong><br>
                    Atas Nama: <strong class="text-gray-900 dark:text-white">{{ $setting->defaultBankAccount->nama_pemilik_rekening }}</strong>
                </p>
            </div>
            @endif

            <!-- Action Button (Tidak tampil saat di-print) -->
            <div class="mt-8 pt-4 border-t border-gray-100 dark:border-gray-700/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 print:hidden">
                <p class="text-xs text-gray-400">
                    Pastikan mengecek komponen biaya sebelum mengirimkan tagihan ke penghuni.
                </p>
                <div class="flex items-center gap-2">
                    <x-ui.button variant="warning" size="sm" href="{{ route('invoices.edit', $invoice) }}">
                        Edit Komponen Biaya
                    </x-ui.button>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
