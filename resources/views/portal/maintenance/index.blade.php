<x-portal-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Header & Action -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Layanan Perbaikan & Maintenance
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Ajukan keluhan kerusakan fasilitas kamar dan pantau tindak lanjut perbaikan oleh tim pengelola.
                </p>
            </div>
            @if ($activeContract)
                <div>
                    <a href="{{ route('portal.maintenance.create') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Lapor Kerusakan Baru
                    </a>
                </div>
            @endif
        </div>

        @if (! $tenant)
            <!-- Unlinked Tenant State -->
            <div class="p-8 text-center rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-sm">
                <h3 class="text-base font-bold">Akun Belum Terhubung</h3>
                <p class="text-xs text-amber-700 dark:text-amber-300 mt-1 max-w-md mx-auto">
                    Akun Anda belum memiliki data penghuni kost aktif.
                </p>
            </div>
        @elseif (! $activeContract)
            <!-- No Active Room Notice -->
            <div class="p-6 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-sm flex items-start gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-sm font-bold text-amber-900 dark:text-amber-200">Belum Ada Kamar Aktif</h3>
                    <p class="text-xs text-amber-700 dark:text-amber-300 mt-0.5">
                        Anda saat ini belum memiliki kontrak kamar yang sedang aktif. Layanan pelaporan perbaikan hanya tersedia bagi penghuni yang sedang menempati kamar.
                    </p>
                </div>
            </div>
        @endif

        @if ($tenant)
            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <a href="{{ route('portal.maintenance.index') }}"
                   class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ !request()->filled('status') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                    Semua
                </a>
                @foreach ($statuses as $st)
                    <a href="{{ route('portal.maintenance.index', ['status' => $st->value]) }}"
                       class="px-3.5 py-1.5 rounded-full font-semibold whitespace-nowrap transition-colors {{ request('status') === $st->value ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800' }}">
                        {{ $st->label() }}
                    </a>
                @endforeach
            </div>

            <!-- Maintenance Requests List -->
            @if ($requests->isEmpty())
                <div class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">Tidak Ada Permintaan Perbaikan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 max-w-sm mx-auto">
                        Belum ada tiket laporan kerusakan yang tercatat pada filter ini.
                    </p>
                    @if ($activeContract)
                        <div class="mt-4">
                            <a href="{{ route('portal.maintenance.create') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                                Buat Laporan Baru &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="space-y-3">
                    @foreach ($requests as $item)
                        <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-slate-300 dark:hover:border-slate-700 transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-mono text-xs font-bold text-slate-900 dark:text-white">
                                            #{{ $item->ticket_number }}
                                        </span>
                                        <x-ui.badge :status="$item->status">
                                            {{ $item->status->label() }}
                                        </x-ui.badge>
                                        <x-ui.badge :status="$item->priority">
                                            {{ $item->priority->label() }}
                                        </x-ui.badge>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                            {{ $item->category->label() }}
                                        </span>
                                    </div>
                                    <div class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                                        Kamar {{ $item->room?->room_number ?? '-' }} &bull; {{ $item->location }}
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2">
                                        {{ $item->description }}
                                    </p>
                                </div>

                                <div class="flex sm:flex-col items-center sm:items-end justify-between sm:justify-center gap-2 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100 dark:border-slate-800">
                                    <span class="text-[11px] text-slate-400 dark:text-slate-500">
                                        {{ $item->reported_at ? $item->reported_at->format('d M Y, H:i') : $item->created_at->format('d M Y, H:i') }}
                                    </span>
                                    <a href="{{ route('portal.maintenance.show', $item) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 transition-colors">
                                        Detail Tiket
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($requests->hasPages())
                    <div class="pt-4">
                        {{ $requests->links() }}
                    </div>
                @endif
            @endif
        @endif
    </div>
</x-portal-layout>
