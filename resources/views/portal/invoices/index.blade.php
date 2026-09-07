<x-portal-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Header & Nav Tabs -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Tagihan & Pembayaran
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Kelola tagihan sewa kamar bulanan, lakukan transfer, dan unduh kuitansi resmi.
                </p>
            </div>
            <!-- Tab Switcher -->
            <div class="inline-flex p-1 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700/80">
                <a href="{{ route('portal.invoices.index') }}"
                   class="px-4 py-2 rounded-lg text-xs font-bold transition-colors bg-white dark:bg-slate-900 text-emerald-600 dark:text-emerald-400 shadow-sm">
                    Daftar Tagihan
                </a>
                <a href="{{ route('portal.payments.index') }}"
                   class="px-4 py-2 rounded-lg text-xs font-semibold transition-colors text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">
                    Riwayat Pembayaran
                </a>
            </div>
        </div>

        @if (! $tenant)
            <!-- Unlinked Tenant State -->
            <div class="p-8 text-center rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-sm">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold">Akun Belum Terhubung</h3>
                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1 max-w-md mx-auto">
                    Akun login Anda belum terhubung dengan data penghuni kost. Tagihan akan otomatis muncul setelah pengelola mengaktifkan kontrak sewa Anda.
                </p>
            </div>
        @else
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tagihan</span>
                    <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ $totalInvoices }}</p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Invoice sewa diterbitkan</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tagihan Belum Lunas</span>
                    <p class="text-2xl font-extrabold {{ $unpaidCount > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400' }} mt-1">
                        {{ $unpaidCount }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Perlu diselesaikan</span>
                </div>

                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sisa Kewajiban Bayar</span>
                    <p class="text-2xl font-extrabold {{ $totalUnpaidAmount > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-slate-900 dark:text-white' }} mt-1">
                        Rp {{ number_format($totalUnpaidAmount, 0, ',', '.') }}
                    </p>
                    <span class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 block">Total sisa balance</span>
                </div>
            </div>

            <!-- Filter Status Bar -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('portal.invoices.index') }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ !request()->filled('status') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua
                </a>
                @foreach ($statuses as $st)
                    <a href="{{ route('portal.invoices.index', ['status' => $st->value]) }}"
                       class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ request('status') === $st->value ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        {{ $st->label() }}
                    </a>
                @endforeach
            </div>

            <!-- Invoice List -->
            @if ($invoices->isEmpty())
                <div class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tidak Ada Tagihan Ditemukan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Belum ada tagihan sewa pada kategori status yang dipilih.
                    </p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($invoices as $inv)
                        @php
                            $paidAmount = $inv->paidAmount();
                            $remaining = $inv->remainingBalance();
                            $isOverdue = $inv->isOverdue();
                        @endphp
                        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1 space-y-2">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400">
                                        #INV-{{ $inv->year }}{{ str_pad($inv->month, 2, '0', STR_PAD_LEFT) }}-{{ str_pad($inv->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <x-ui.badge :status="$inv->status">
                                        {{ $inv->status->label() }}
                                    </x-ui.badge>
                                    @if ($isOverdue && !$inv->isPaid())
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 dark:bg-rose-950 dark:text-rose-300">
                                            Lewat Jatuh Tempo
                                        </span>
                                    @endif
                                </div>

                                <div>
                                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                        Tagihan Sewa Bulan {{ \Carbon\Carbon::createFromDate($inv->year, $inv->month, 1)->translatedFormat('F Y') }}
                                    </h3>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        Kamar {{ $inv->room?->room_number }} (Lt. {{ $inv->room?->floor }}) • Jatuh tempo: <strong>{{ $inv->due_date->translatedFormat('d F Y') }}</strong>
                                    </p>
                                </div>

                                <!-- Progress Bar if Partial Payment -->
                                @if ($paidAmount > 0 && !$inv->isPaid())
                                    <div class="max-w-md pt-1">
                                        <div class="flex justify-between text-[11px] text-slate-500 dark:text-slate-400 mb-1">
                                            <span>Terbayar: Rp {{ number_format($paidAmount, 0, ',', '.') }}</span>
                                            <span>Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="w-full h-1.5 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: {{ min(100, round(($paidAmount / $inv->total_amount) * 100)) }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col sm:flex-row md:flex-col lg:flex-row items-start sm:items-center md:items-end lg:items-center justify-between gap-3 pt-3 md:pt-0 border-t md:border-t-0 border-slate-100 dark:border-slate-800">
                                <div class="text-left md:text-right">
                                    <span class="text-xs text-slate-400 block">Total Tagihan</span>
                                    <span class="text-lg font-extrabold text-slate-900 dark:text-white">
                                        Rp {{ number_format($inv->total_amount, 0, ',', '.') }}
                                    </span>
                                    @if ($remaining > 0 && $remaining < $inv->total_amount)
                                        <span class="block text-[11px] text-rose-600 dark:text-rose-400 font-semibold">
                                            Sisa: Rp {{ number_format($remaining, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('portal.invoices.show', $inv) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition-colors shadow-sm {{ $inv->isPaid() ? 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300' : 'bg-emerald-600 hover:bg-emerald-700 text-white' }}">
                                    <span>{{ $inv->isPaid() ? 'Lihat Rincian' : 'Rincian & Bayar' }}</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $invoices->links() }}
                </div>
            @endif
        @endif
    </div>
</x-portal-layout>
