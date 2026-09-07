<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Back & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('portal.permissions.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Izin
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $permission->title }}
                    </h1>
                    <x-ui.badge :status="$permission->status">
                        {{ $permission->status->label() }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Permohonan Card -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        Rincian Permohonan Izin
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Jenis Izin</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                {{ $permission->type->label() }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Waktu Diajukan</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                {{ $permission->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        @if ($permission->start_at || $permission->end_at)
                            <div class="sm:col-span-2">
                                <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Periode Izin</span>
                                <span class="font-semibold text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                    {{ $permission->start_at ? $permission->start_at->format('d M Y, H:i') : '-' }}
                                    @if ($permission->end_at)
                                        s/d {{ $permission->end_at->format('d M Y, H:i') }}
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    <div>
                        <span class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold mb-1">Keterangan & Rincian</span>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-slate-800 dark:text-slate-200 text-sm whitespace-pre-line leading-relaxed">
                            {{ $permission->description }}
                        </div>
                    </div>
                </div>

                <!-- Respon Staf / Keputusan Card -->
                @if ($permission->reviewed_at || $permission->review_note || $permission->isApproved() || $permission->isRejected())
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                            Keputusan Pengelola Kost
                        </h2>

                        @if ($permission->isApproved())
                            <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm">
                                <span class="font-bold block text-xs uppercase tracking-wider mb-1">Status: Disetujui</span>
                                @if ($permission->review_note)
                                    <p class="leading-relaxed">{{ $permission->review_note }}</p>
                                @else
                                    <p class="leading-relaxed">Permohonan izin Anda telah disetujui oleh pengelola kost.</p>
                                @endif
                            </div>
                        @elseif ($permission->isRejected())
                            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm">
                                <span class="font-bold block text-xs uppercase tracking-wider mb-1">Status: Ditolak</span>
                                <p class="leading-relaxed">{{ $permission->review_note ?? 'Permohonan izin belum dapat disetujui.' }}</p>
                            </div>
                        @endif

                        @if ($permission->reviewed_at)
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                Ditinjau pada: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $permission->reviewed_at->format('d M Y, H:i') }} WIB</span>
                                @if ($permission->reviewer)
                                    oleh <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $permission->reviewer->name }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Right 1 Col: Status Tracker & Actions -->
            <div class="space-y-6">
                <!-- Status Timeline Tracker -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Progress Permohonan
                    </h3>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700">
                        <!-- Step 1: Diajukan -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Izin Diajukan</div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $permission->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <!-- Step 2: Keputusan -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full {{ $permission->isApproved() ? 'bg-emerald-500' : ($permission->isRejected() ? 'bg-rose-500' : ($permission->isCancelled() ? 'bg-slate-400' : 'bg-amber-400')) }} ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="text-xs font-bold {{ $permission->isApproved() ? 'text-emerald-600 dark:text-emerald-400' : ($permission->isRejected() ? 'text-rose-600 dark:text-rose-400' : ($permission->isCancelled() ? 'text-slate-500' : 'text-amber-600 dark:text-amber-400')) }}">
                                {{ $permission->status->label() }}
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                @if ($permission->isPending())
                                    Menunggu verifikasi staf
                                @elseif ($permission->reviewed_at)
                                    Ditinjau {{ $permission->reviewed_at->format('d M Y, H:i') }}
                                @else
                                    {{ $permission->status->label() }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Room Info Card -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Informasi Kamar
                    </h3>
                    <div class="text-xs space-y-2 text-slate-600 dark:text-slate-400">
                        <div class="flex justify-between">
                            <span>Nomor Kamar:</span>
                            <span class="font-bold text-slate-900 dark:text-white">Kamar {{ $permission->contract?->room?->room_number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Lantai:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Lantai {{ $permission->contract?->room?->floor ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Cancel Action (Pending only) -->
                @if ($permission->isPending())
                    <div class="p-6 rounded-2xl bg-rose-50/50 dark:bg-rose-950/20 border border-rose-200 dark:border-rose-900/50 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">
                            Batalkan Permohonan
                        </h3>
                        <p class="text-xs text-rose-600 dark:text-rose-400">
                            Jika permohonan izin ini sudah tidak diperlukan, Anda dapat membatalkannya selama masih berstatus menunggu.
                        </p>
                        <form action="{{ route('portal.permissions.cancel', $permission) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permohonan izin ini?');">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="w-full py-2 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-sm transition-colors">
                                Batalkan Izin Ini
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-portal-layout>
