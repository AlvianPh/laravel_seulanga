<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Back & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('portal.move-outs.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Move-Out
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Rincian Permohonan Move-Out
                    </h1>
                    <x-ui.badge :status="$moveOut->status">
                        {{ $moveOut->status->label() }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details & Settlement -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Pengajuan Card -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        Informasi Rencana Keluar Kost
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Kamar</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                Kamar {{ $moveOut->contract?->room?->room_number ?? '-' }} (Lantai {{ $moveOut->contract?->room?->floor ?? '-' }})
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Rencana Tanggal Keluar</span>
                            <span class="font-bold text-rose-600 dark:text-rose-400 text-sm mt-0.5 inline-block">
                                {{ $moveOut->requested_move_out_date->format('d M Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Waktu Pengajuan</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                {{ $moveOut->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Status Permohonan</span>
                            <span class="mt-0.5 inline-block">
                                <x-ui.badge :status="$moveOut->status">
                                    {{ $moveOut->status->label() }}
                                </x-ui.badge>
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold mb-1">Alasan Keluar</span>
                        <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-slate-800 dark:text-slate-200 text-sm leading-relaxed whitespace-pre-line">
                            {{ $moveOut->reason }}
                        </div>
                    </div>

                    @if ($moveOut->notes)
                        <div>
                            <span class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold mb-1">Catatan Tambahan Penghuni</span>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-slate-700 dark:text-slate-300 text-xs leading-relaxed whitespace-pre-line">
                                {{ $moveOut->notes }}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Review / Decision Card if reviewed -->
                @if ($moveOut->reviewed_at || $moveOut->review_note)
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                            Hasil Review Pengelola
                        </h2>

                        <div class="p-4 rounded-xl {{ $moveOut->isRejected() ? 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 text-rose-800 dark:text-rose-200' : 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 text-emerald-800 dark:text-emerald-200' }} text-sm">
                            <span class="font-bold block mb-1">
                                {{ $moveOut->isRejected() ? 'Permohonan Ditolak' : 'Permohonan Disetujui' }}
                            </span>
                            @if ($moveOut->review_note)
                                <p class="text-xs whitespace-pre-line">{{ $moveOut->review_note }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Inspeksi Kamar Card if inspected -->
                @if ($moveOut->inspected_at)
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                            Hasil Inspeksi Fisik Kamar
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Kondisi Kamar</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block capitalize">
                                    {{ str_replace('_', ' ', $moveOut->room_condition ?? 'Baik') }}
                                </span>
                            </div>
                            <div>
                                <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Estimasi Biaya Kerusakan</span>
                                <span class="font-bold {{ $moveOut->damage_cost > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400' }} text-sm mt-0.5 inline-block">
                                    Rp {{ number_format($moveOut->damage_cost, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        @if ($moveOut->damage_notes)
                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold mb-1">Catatan Kerusakan</span>
                                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-slate-800 dark:text-slate-200 text-xs leading-relaxed whitespace-pre-line">
                                    {{ $moveOut->damage_notes }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Settlement Finansial Card -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        Rincian Perhitungan Settlement & Deposit
                    </h2>

                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">(+) Nilai Deposit Awal Kontrak:</span>
                            <span class="font-bold text-slate-900 dark:text-white">
                                Rp {{ number_format($settlement['deposit_amount'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">(-) Sisa Tagihan Belum Lunas (Invoices):</span>
                            <span class="font-semibold text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($settlement['outstanding_invoices_amount'], 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400 font-medium">(-) Potongan Biaya Kerusakan:</span>
                            <span class="font-semibold text-rose-600 dark:text-rose-400">
                                Rp {{ number_format($settlement['damage_deduction_amount'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Final Calculated Outcome -->
                    <div class="p-4 rounded-xl {{ $settlement['remaining_tenant_liability'] > 0 ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-900/50' : 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-900/50' }} border space-y-2">
                        @if ($settlement['remaining_tenant_liability'] > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">
                                    Sisa Kewajiban yang Harus Dibayar:
                                </span>
                                <span class="text-base font-extrabold text-rose-700 dark:text-rose-400">
                                    Rp {{ number_format($settlement['remaining_tenant_liability'], 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="text-[11px] text-rose-600 dark:text-rose-400">
                                Total tagihan dan kerusakan melebihi nilai deposit. Silakan hubungi pengelola untuk menyelesaikan sisa pembayaran.
                            </p>
                        @else
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">
                                    Deposit yang Dikembalikan:
                                </span>
                                <span class="text-base font-extrabold text-emerald-700 dark:text-emerald-400">
                                    Rp {{ number_format($settlement['deposit_returned_amount'], 0, ',', '.') }}
                                </span>
                            </div>
                            <p class="text-[11px] text-emerald-600 dark:text-emerald-400">
                                Saldo pengembalian deposit akan ditransfer pengelola sesuai catatan rekening Anda.
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Status Progress Tracker & Actions -->
            <div class="space-y-6">
                <!-- Status Timeline Tracker -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Progress Move-Out
                    </h3>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700 text-xs">
                        <!-- Step 1: Diajukan -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="font-bold text-slate-900 dark:text-white">1. Pengajuan Move-Out</div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $moveOut->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <!-- Step 2: Review Staf -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full {{ $moveOut->isApproved() || $moveOut->isInspection() || $moveOut->isSettlement() || $moveOut->isCompleted() ? 'bg-emerald-500' : ($moveOut->isRejected() ? 'bg-rose-500' : ($moveOut->isCancelled() ? 'bg-slate-400' : 'bg-amber-400')) }} ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="font-bold {{ $moveOut->isRejected() ? 'text-rose-600' : 'text-slate-900 dark:text-white' }}">2. Persetujuan Pengelola</div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $moveOut->reviewed_at ? $moveOut->reviewed_at->format('d M Y') : 'Menunggu review' }}
                            </p>
                        </div>

                        <!-- Step 3: Inspeksi Kamar -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full {{ $moveOut->isInspection() || $moveOut->isSettlement() || $moveOut->isCompleted() ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700' }} ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="font-bold text-slate-900 dark:text-white">3. Inspeksi Fisik Kamar</div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $moveOut->inspected_at ? $moveOut->inspected_at->format('d M Y') : 'Pemeriksaan kondisi' }}
                            </p>
                        </div>

                        <!-- Step 4: Selesai / Kontrak Berakhir -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full {{ $moveOut->isCompleted() ? 'bg-emerald-500' : 'bg-slate-300 dark:bg-slate-700' }} ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="font-bold text-slate-900 dark:text-white">4. Settlement & Selesai</div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $moveOut->completed_at ? $moveOut->completed_at->format('d M Y') : 'Pengakhiran kontrak' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cancel Action (Pending only) -->
                @if ($moveOut->isPending())
                    <div class="p-6 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">
                            Batalkan Permohonan
                        </h3>
                        <p class="text-xs text-rose-600 dark:text-rose-400">
                            Jika Anda berubah pikiran atau menunda tanggal keluar, permohonan dapat dibatalkan selama masih berstatus menunggu review.
                        </p>
                        <form action="{{ route('portal.move-outs.cancel', $moveOut) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan move-out ini?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full py-2 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-sm transition-colors">
                                Batalkan Move-Out Ini
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-portal-layout>
