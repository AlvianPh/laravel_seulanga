<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Riwayat Pengajuan Kamar</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pantau status verifikasi dan review pengajuan sewa kamar Anda.
                </p>
            </div>
            <a href="{{ route('portal.rooms.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Ajukan Kamar Lain
            </a>
        </div>

        @if ($applications->isEmpty())
            <div class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Belum Ada Pengajuan Kamar</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                    Anda belum pernah mengirimkan pengajuan sewa kamar kost. Cari kamar yang cocok dan ajukan sekarang.
                </p>
                <a href="{{ route('portal.rooms.index') }}" class="inline-flex items-center gap-2 py-2.5 px-5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                    Jelajahi Kamar Tersedia
                </a>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($applications as $app)
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100 dark:border-slate-800">
                            <div>
                                <div class="flex items-center gap-2.5">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                                        Kamar {{ $app->room?->room_number }}
                                    </h3>
                                    <span class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        {{ $app->room?->roomType?->name ?? 'Standard' }} • Lantai {{ $app->room?->floor }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                    Diajukan pada: {{ $app->created_at->translatedFormat('d F Y, H:i') }}
                                </p>
                            </div>

                            <div>
                                <x-ui.badge :variant="$app->status->badgeVariant()">
                                    {{ $app->status->label() }}
                                </x-ui.badge>
                            </div>
                        </div>

                        <!-- Notes & Detail -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div>
                                <span class="text-slate-400">Harga Sewa:</span>
                                <p class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5">
                                    Rp {{ number_format($app->room?->monthly_price ?? 0, 0, ',', '.') }} / bulan
                                </p>
                            </div>
                            <div>
                                <span class="text-slate-400">Catatan Anda:</span>
                                <p class="text-slate-700 dark:text-slate-300 mt-0.5">
                                    {{ $app->application_notes ?: '-' }}
                                </p>
                            </div>
                        </div>

                        <!-- Status Feedback Message -->
                        @if ($app->status === \App\Enums\StatusApplication::Approved)
                            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200 text-xs flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-bold">Pengajuan Disetujui!</p>
                                    <p class="mt-0.5">
                                        Pengajuan kamar Anda telah disetujui oleh pengelola kost. Silakan menunggu proses pembuatan surat kontrak sewa resmi.
                                    </p>
                                </div>
                            </div>
                        @elseif ($app->status === \App\Enums\StatusApplication::Rejected)
                            <div class="p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-900 dark:text-red-200 text-xs flex items-start gap-2.5">
                                <svg class="w-4 h-4 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
                                    <p class="font-bold">Pengajuan Ditolak</p>
                                    <p class="mt-0.5">
                                        <strong>Alasan:</strong> {{ $app->rejection_reason ?: 'Tidak memenuhi persyaratan ketersediaan.' }}
                                    </p>
                                </div>
                            </div>
                        @elseif ($app->status === \App\Enums\StatusApplication::Pending)
                            <div class="pt-2 flex items-center justify-between">
                                <span class="text-xs text-amber-600 dark:text-amber-400 font-medium">
                                    Sedang ditinjau oleh staf pengelola...
                                </span>
                                <form method="POST" action="{{ route('portal.applications.cancel', $app) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan ini?');">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-semibold text-red-600 dark:text-red-400 hover:underline">
                                        Batalkan Pengajuan
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach

                <div class="mt-6">
                    {{ $applications->links() }}
                </div>
            </div>
        @endif
    </div>
</x-portal-layout>
