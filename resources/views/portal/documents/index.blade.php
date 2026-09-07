<x-portal-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Dokumen & Berkas Penghuni
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Kelola berkas identitas, syarat sewa, dokumen tata tertib kontrak, dan kuitansi pembayaran resmi Anda.
                </p>
            </div>
            @if ($tenant)
                <div>
                    <a href="{{ route('portal.documents.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Unggah Dokumen
                    </a>
                </div>
            @endif
        </div>

        @if (! $tenant)
            <!-- Unlinked Tenant State -->
            <div class="p-8 text-center rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-sm">
                <h3 class="text-base font-bold">Akun Belum Terhubung</h3>
                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1 max-w-md mx-auto">
                    Akun Anda belum memiliki data penghuni kost aktif. Silakan hubungi admin kost.
                </p>
            </div>
        @else
            <!-- Overview & Quick Access Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Contract & Agreement Card -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Perjanjian Sewa
                            </span>
                            @if ($activeContract)
                                <span class="text-xs text-slate-400">Kamar {{ $activeContract->room->room_number ?? '-' }}</span>
                            @endif
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Tata Tertib & Perjanjian Kontrak</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            @if ($activeContract && $activeContract->isAgreementAccepted())
                                Telah disetujui secara digital pada {{ $activeContract->agreement_accepted_at->translatedFormat('d F Y H:i') }} (Versi {{ $activeContract->agreement_version ?? '1.0' }}).
                            @elseif ($activeContract)
                                Menunggu persetujuan tata tertib digital.
                            @else
                                Belum ada kontrak sewa aktif saat ini.
                            @endif
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Status Sewa: <strong class="text-slate-800 dark:text-slate-200">{{ $activeContract ? $activeContract->status->label() : 'Tidak Aktif' }}</strong></span>
                        <a href="{{ route('portal.contract.index') }}" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline flex items-center gap-1">
                            Lihat Kontrak
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Verified Payment Receipts Card -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Kuitansi Resmi
                            </span>
                            <span class="text-xs text-slate-400">{{ $verifiedPayments->count() }} Pembayaran Terverifikasi</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">Kuitansi Pembayaran Kost</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Akses kuitansi resmi bertanda tangan digital pengelola untuk pembayaran yang telah disetujui.
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs text-slate-500 dark:text-slate-400">Riwayat Pembayaran</span>
                        <a href="{{ route('portal.payments.index') }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                            Lihat Kuitansi
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                <!-- Status Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <a href="{{ route('portal.documents.index', array_filter(['type' => request('type')])) }}"
                       class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ !request()->filled('status') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        Semua Status
                    </a>
                    @foreach ($statuses as $st)
                        <a href="{{ route('portal.documents.index', array_filter(['status' => $st->value, 'type' => request('type')])) }}"
                           class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ request('status') === $st->value ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                            {{ $st->label() }}
                        </a>
                    @endforeach
                </div>

                <!-- Type Filter Dropdown -->
                <form method="GET" action="{{ route('portal.documents.index') }}" class="flex items-center gap-2">
                    @if (request()->filled('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <select name="type" onchange="this.form.submit()"
                            class="text-xs py-1.5 px-3 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                        <option value="">Semua Jenis Dokumen</option>
                        @foreach ($types as $tp)
                            <option value="{{ $tp->value }}" {{ request('type') === $tp->value ? 'selected' : '' }}>
                                {{ $tp->label() }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>

            <!-- Documents List -->
            @if ($documents->isEmpty())
                <div class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Belum Ada Dokumen</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Anda belum mengunggah dokumen apapun. Klik tombol di bawah untuk mengunggah dokumen KTP atau berkas pendukung lainnya.
                    </p>
                    <div class="mt-4">
                        <a href="{{ route('portal.documents.create') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Unggah Dokumen Sekarang
                        </a>
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($documents as $doc)
                        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-all flex flex-col justify-between">
                            <div>
                                <!-- Top Badges -->
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                        {{ $doc->type->label() }}
                                    </span>
                                    @php
                                        $badgeColor = match($doc->status) {
                                            \App\Enums\DocumentStatus::Verified => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800',
                                            \App\Enums\DocumentStatus::Rejected => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800',
                                            default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $badgeColor }}">
                                        {{ $doc->status->label() }}
                                    </span>
                                </div>

                                <!-- Title & Info -->
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white leading-tight">
                                    <a href="{{ route('portal.documents.show', $doc) }}" class="hover:text-emerald-600 dark:hover:text-emerald-400">
                                        {{ $doc->title }}
                                    </a>
                                </h3>

                                <div class="flex items-center gap-2 text-[11px] text-slate-500 dark:text-slate-400 mt-2">
                                    <span class="truncate max-w-[150px]">{{ $doc->file_name }}</span>
                                    <span>•</span>
                                    <span>{{ $doc->formattedSize() }}</span>
                                </div>

                                @if ($doc->rejection_reason && $doc->status === \App\Enums\DocumentStatus::Rejected)
                                    <div class="mt-3 p-2.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/50 text-[11px] text-rose-700 dark:text-rose-300">
                                        <strong>Alasan Ditolak:</strong> {{ $doc->rejection_reason }}
                                    </div>
                                @endif
                            </div>

                            <!-- Bottom Action Buttons -->
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                                <span class="text-[11px] text-slate-400">
                                    {{ $doc->created_at->translatedFormat('d M Y') }}
                                </span>
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('portal.documents.preview', $doc) }}" target="_blank"
                                       class="p-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
                                       title="Buka / Pratinjau">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('portal.documents.download', $doc) }}"
                                       class="p-1.5 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
                                       title="Unduh Berkas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    <a href="{{ route('portal.documents.show', $doc) }}"
                                       class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-[11px] transition-colors">
                                        Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($documents->hasPages())
                    <div class="pt-4">
                        {{ $documents->links() }}
                    </div>
                @endif
            @endif
        @endif
    </div>
</x-portal-layout>
