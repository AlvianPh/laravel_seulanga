<x-portal-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Header & Nav Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Riwayat Pembayaran
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Arsip seluruh transaksi pembayaran dan bukti transfer yang telah Anda kirimkan.
                </p>
            </div>
            <!-- Tab Switcher -->
            <div class="inline-flex p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80">
                <a href="{{ route('portal.invoices.index') }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-colors text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    Daftar Tagihan
                </a>
                <a href="{{ route('portal.payments.index') }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-colors bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm">
                    Riwayat Pembayaran
                </a>
            </div>
        </div>

        @if (! $tenant)
            <!-- Unlinked Tenant State -->
            <div class="p-8 text-center rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-sm">
                <h3 class="text-base font-bold">Akun Belum Terhubung</h3>
                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1 max-w-md mx-auto">
                    Akun Anda belum memiliki data penghuni kost aktif.
                </p>
            </div>
        @else
            <!-- Filter Status Bar -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('portal.payments.index') }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ !request()->filled('status') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua
                </a>
                @foreach ($statuses as $st)
                    <a href="{{ route('portal.payments.index', ['status' => $st->value]) }}"
                       class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ request('status') === $st->value ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        {{ $st->label() }}
                    </a>
                @endforeach
            </div>

            <!-- Payment List -->
            @if ($payments->isEmpty())
                <div class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Transaksi Pembayaran</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Anda belum mengirimkan bukti pembayaran pada kategori ini.
                    </p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($payments as $pay)
                        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-slate-600 dark:text-slate-300">
                                        {{ $pay->receiptNumber() }}
                                    </span>
                                    <x-ui.badge :status="$pay->status">
                                        {{ $pay->status->label() }}
                                    </x-ui.badge>
                                </div>

                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                        Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Untuk Tagihan: 
                                        @if ($pay->invoice)
                                            <a href="{{ route('portal.invoices.show', $pay->invoice) }}" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                                                Bulan {{ \Carbon\Carbon::createFromDate($pay->invoice->year, $pay->invoice->month, 1)->translatedFormat('F Y') }} (Kamar {{ $pay->invoice->room?->room_number }})
                                            </a>
                                        @else
                                            -
                                        @endif
                                        • {{ $pay->paymentMethod?->name ?? 'Transfer' }} • {{ $pay->payment_date->translatedFormat('d F Y') }}
                                    </p>
                                </div>

                                @if ($pay->isRejected() && $pay->notes)
                                    <div class="p-2.5 rounded-lg bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-[11px] text-rose-700 dark:text-rose-300">
                                        <strong>Alasan Penolakan:</strong> {{ $pay->notes }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <a href="{{ route('portal.payments.show', $pay) }}"
                                   class="inline-flex items-center gap-1 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors">
                                    Detail
                                </a>

                                @if ($pay->isVerified())
                                    <a href="{{ route('portal.payments.receipt', $pay) }}"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                        </svg>
                                        Kuitansi
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $payments->links() }}
                </div>
            @endif
        @endif
    </div>
</x-portal-layout>
