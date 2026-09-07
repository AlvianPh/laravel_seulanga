<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Back Navigation -->
        <div class="flex items-center justify-between">
            <a href="{{ route('portal.invoices.index') }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Daftar Tagihan
            </a>
            <x-ui.badge :status="$invoice->status">
                {{ $invoice->status->label() }}
            </x-ui.badge>
        </div>

        @php
            $paidAmount = $invoice->paidAmount();
            $remaining = $invoice->remainingBalance();
            $isPaid = $invoice->isPaid();
            $isOverdue = $invoice->isOverdue();
        @endphp

        <!-- Invoice Header Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Nomor Tagihan</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                        #INV-{{ $invoice->year }}{{ str_pad($invoice->month, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Periode: <strong>{{ \Carbon\Carbon::createFromDate($invoice->year, $invoice->month, 1)->translatedFormat('F Y') }}</strong>
                        • Kamar <strong>{{ $invoice->room?->room_number }}</strong> (Lt. {{ $invoice->room?->floor }})
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tagihan</span>
                    <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </p>
                    <span class="text-xs {{ $isOverdue && !$isPaid ? 'text-rose-600 dark:text-rose-400 font-bold' : 'text-slate-500 dark:text-slate-400' }}">
                        Jatuh Tempo: {{ $invoice->due_date->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>

            <!-- Balance Status Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-400">Jumlah Tagihan</span>
                    <p class="text-lg font-bold text-slate-900 dark:text-white mt-0.5">
                        Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-400">Sudah Dibayar (Sah)</span>
                    <p class="text-lg font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                        Rp {{ number_format($paidAmount, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-400">Sisa Kewajiban</span>
                    <p class="text-lg font-bold {{ $remaining > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} mt-0.5">
                        Rp {{ number_format($remaining, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Itemized Fee Breakdown Table -->
            <div class="mt-6">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Rincian Komponen Biaya</h3>
                <div class="rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-800 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Deskripsi Item</th>
                                <th class="py-3 px-4 text-right">Nominal (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-slate-700 dark:text-slate-300">
                            <tr>
                                <td class="py-3 px-4">Biaya Sewa Kamar Pokok</td>
                                <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($invoice->rent_amount, 0, ',', '.') }}</td>
                            </tr>
                            @if ($invoice->electricity_fee > 0)
                                <tr>
                                    <td class="py-3 px-4">Biaya Listrik / Tambahan Elektronik</td>
                                    <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($invoice->electricity_fee, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($invoice->water_fee > 0)
                                <tr>
                                    <td class="py-3 px-4">Biaya Air Bersih</td>
                                    <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($invoice->water_fee, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($invoice->internet_fee > 0)
                                <tr>
                                    <td class="py-3 px-4">Biaya Layanan Internet (Wi-Fi)</td>
                                    <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($invoice->internet_fee, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($invoice->penalty_fee > 0)
                                <tr>
                                    <td class="py-3 px-4 text-rose-600 dark:text-rose-400">Denda Keterlambatan</td>
                                    <td class="py-3 px-4 text-right font-medium text-rose-600 dark:text-rose-400">Rp {{ number_format($invoice->penalty_fee, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            @if ($invoice->other_fee > 0)
                                <tr>
                                    <td class="py-3 px-4">Biaya Lain-lain</td>
                                    <td class="py-3 px-4 text-right font-medium">Rp {{ number_format($invoice->other_fee, 0, ',', '.') }}</td>
                                </tr>
                            @endif
                            <tr class="bg-slate-50/50 dark:bg-slate-800/40 font-bold text-slate-900 dark:text-white">
                                <td class="py-3.5 px-4 text-sm">Total Tagihan</td>
                                <td class="py-3.5 px-4 text-right text-sm text-emerald-600 dark:text-emerald-400">
                                    Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Bank Accounts Information Card -->
        @if ($bankAccounts->isNotEmpty() && !$isPaid)
            <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Rekening Tujuan Pembayaran Kost</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Silakan lakukan transfer ke salah satu rekening resmi pengelola:</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach ($bankAccounts as $bank)
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex items-center justify-between">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white uppercase">{{ $bank->nama_bank }}</span>
                                <p class="text-base font-mono font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5 tracking-wider">{{ $bank->nomor_rekening }}</p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">a.n {{ $bank->nama_pemilik_rekening }}</p>
                            </div>
                            <div class="text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-1 rounded">
                                Transfer Bank
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Submit Payment Form Card (If Not Fully Paid & Not Cancelled) -->
        @if ($remaining > 0 && $invoice->status !== \App\Enums\StatusTagihan::Cancelled)
            <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm"
                 x-data="{ submitting: false, paymentAmount: '{{ (int)$remaining }}' }">
                <div class="flex items-center gap-2.5 pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                    <div class="w-8 h-8 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Form Konfirmasi Pembayaran</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Unggah bukti transfer untuk diverifikasi oleh pengelola kost.</p>
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('portal.invoices.payments.store', $invoice) }}"
                      enctype="multipart/form-data"
                      @submit="submitting = true"
                      class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nominal Bayar -->
                        <div>
                            <label for="amount" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Nominal yang Dibayar (Rp) <span class="text-rose-500">*</span>
                            </label>
                            <input type="number"
                                   id="amount"
                                   name="amount"
                                   min="1"
                                   x-model="paymentAmount"
                                   value="{{ old('amount', (int)$remaining) }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl border text-sm font-semibold border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                            <p class="text-[11px] text-slate-400 mt-1">Sisa tagihan: Rp {{ number_format($remaining, 0, ',', '.') }}</p>
                            @error('amount')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Metode Pembayaran -->
                        <div>
                            <label for="payment_method_id" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Saluran / Metode Pembayaran <span class="text-rose-500">*</span>
                            </label>
                            <select id="payment_method_id"
                                    name="payment_method_id"
                                    required
                                    class="w-full px-3.5 py-2.5 rounded-xl border text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                                <option value="">-- Pilih Metode Pembayaran --</option>
                                @foreach ($paymentMethods as $pm)
                                    <option value="{{ $pm->id }}" {{ old('payment_method_id') == $pm->id ? 'selected' : '' }}>
                                        {{ $pm->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('payment_method_id')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tanggal Pembayaran -->
                        <div>
                            <label for="payment_date" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Tanggal Transfer / Bayar <span class="text-rose-500">*</span>
                            </label>
                            <input type="date"
                                   id="payment_date"
                                   name="payment_date"
                                   value="{{ old('payment_date', date('Y-m-d')) }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl border text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">
                            @error('payment_date')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bukti Pembayaran -->
                        <div>
                            <label for="proof_photo" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                Unggah Struk / Bukti Transfer <span class="text-rose-500">*</span>
                            </label>
                            <input type="file"
                                   id="proof_photo"
                                   name="proof_photo"
                                   accept="image/jpeg,image/png,image/jpg,application/pdf"
                                   required
                                   class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950 dark:file:text-emerald-400">
                            <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, PDF (Maks. 2MB)</p>
                            @error('proof_photo')
                                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Catatan Tambahan -->
                    <div>
                        <label for="notes" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                            Catatan (Opsional)
                        </label>
                        <textarea id="notes"
                                  name="notes"
                                  rows="2"
                                  placeholder="Contoh: Transfer via BCA atas nama Pengirim Budi"
                                  class="w-full px-3.5 py-2.5 rounded-xl border text-sm border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-2 focus:ring-emerald-500">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                                :disabled="submitting"
                                class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl text-sm font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg x-show="!submitting" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <svg x-show="submitting" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span x-text="submitting ? 'Mengirim Bukti...' : 'Kirim Bukti Pembayaran'">Kirim Bukti Pembayaran</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Payment History on this Invoice -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">
                Riwayat Pembayaran untuk Tagihan Ini
            </h3>

            @if ($invoice->payments->isEmpty())
                <p class="text-xs text-slate-500 dark:text-slate-400 py-4 text-center">
                    Belum ada riwayat pembayaran yang dikirimkan untuk tagihan ini.
                </p>
            @else
                <div class="space-y-3">
                    @foreach ($invoice->payments->sortByDesc('payment_date') as $pay)
                        <div class="p-4 rounded-xl border border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300">
                                        {{ $pay->receiptNumber() }}
                                    </span>
                                    <x-ui.badge :status="$pay->status">
                                        {{ $pay->status->label() }}
                                    </x-ui.badge>
                                </div>
                                <p class="text-sm font-bold text-slate-900 dark:text-white">
                                    Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Metode: {{ $pay->paymentMethod?->name ?? '-' }} • Tanggal: {{ $pay->payment_date->translatedFormat('d F Y') }}
                                </p>
                                @if ($pay->notes)
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400 italic">
                                        Catatan: {{ $pay->notes }}
                                    </p>
                                @endif
                                @if ($pay->isVerified() && $pay->verifier)
                                    <p class="text-[11px] text-emerald-600 dark:text-emerald-400">
                                        Diverifikasi oleh: {{ $pay->verifier->name }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center gap-2">
                                @if ($pay->proof_path)
                                    <a href="{{ asset('storage/' . $pay->proof_path) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        Bukti Transfer
                                    </a>
                                @endif

                                @if ($pay->isVerified())
                                    <a href="{{ route('portal.payments.receipt', $pay) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Kuitansi Resmi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>
