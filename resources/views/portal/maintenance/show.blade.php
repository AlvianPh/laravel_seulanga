<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Back & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <a href="{{ route('portal.maintenance.index') }}"
                   class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Kembali ke Daftar Perbaikan
                </a>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        Tiket #{{ $maintenance->ticket_number }}
                    </h1>
                    <x-ui.badge :status="$maintenance->status">
                        {{ $maintenance->status->label() }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        <!-- Main Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Kerusakan Card -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                    <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">
                        Rincian Laporan Kerusakan
                    </h2>

                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Kategori</span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                {{ $maintenance->category->label() }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Prioritas</span>
                            <div class="mt-0.5">
                                <x-ui.badge :status="$maintenance->priority">
                                    {{ $maintenance->priority->label() }}
                                </x-ui.badge>
                            </div>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Lokasi Kerusakan</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                {{ $maintenance->location }}
                            </span>
                        </div>
                        <div>
                            <span class="text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold">Waktu Dilaporkan</span>
                            <span class="font-medium text-slate-800 dark:text-slate-200 text-sm mt-0.5 inline-block">
                                {{ $maintenance->reported_at ? $maintenance->reported_at->format('d M Y, H:i') : $maintenance->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <span class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold mb-1">Deskripsi Kendala</span>
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-slate-800 dark:text-slate-200 text-sm whitespace-pre-line leading-relaxed">
                            {{ $maintenance->description }}
                        </div>
                    </div>

                    <!-- Photo Evidence -->
                    @if ($maintenance->photo_path)
                        <div>
                            <span class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold mb-2">Foto Bukti Kerusakan</span>
                            <div class="rounded-xl overflow-hidden border border-slate-200 dark:border-slate-800 max-w-md">
                                <a href="{{ Storage::url($maintenance->photo_path) }}" target="_blank" rel="noopener" class="block group relative">
                                    <img src="{{ Storage::url($maintenance->photo_path) }}" alt="Foto Kerusakan" class="w-full max-h-72 object-cover group-hover:opacity-95 transition-opacity">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold">
                                        Klik untuk melihat ukuran penuh &rarr;
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Respon Staf / Penanganan Card -->
                @if ($maintenance->notes || $maintenance->rejection_reason || $maintenance->isResolved())
                    <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                        <h2 class="text-base font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                            <span>Tindak Lanjut & Respon Pengelola</span>
                        </h2>

                        @if ($maintenance->isRejected() && $maintenance->rejection_reason)
                            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-900 text-rose-800 dark:text-rose-200 text-sm">
                                <span class="font-bold block text-xs uppercase tracking-wider mb-1">Alasan Penolakan:</span>
                                <p class="leading-relaxed">{{ $maintenance->rejection_reason }}</p>
                            </div>
                        @endif

                        @if ($maintenance->notes)
                            <div>
                                <span class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wider block font-semibold mb-1">Catatan Pengerjaan / Teknisi</span>
                                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/60 text-slate-800 dark:text-slate-200 text-sm whitespace-pre-line leading-relaxed">
                                    {{ $maintenance->notes }}
                                </div>
                            </div>
                        @endif

                        @if ($maintenance->resolved_at)
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                Selesai ditangani pada: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $maintenance->resolved_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Right 1 Col: Tracker & Room -->
            <div class="space-y-6">
                <!-- Status Timeline Tracker -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Status Progress
                    </h3>

                    <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200 dark:before:bg-slate-700">
                        <!-- Step 1: Laporan Dibuat -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">Laporan Dikirim</div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $maintenance->reported_at ? $maintenance->reported_at->format('d M Y, H:i') : $maintenance->created_at->format('d M Y, H:i') }}
                            </p>
                        </div>

                        <!-- Step 2: In Progress -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full {{ in_array($maintenance->status->value, ['in_progress', 'resolved']) ? 'bg-emerald-500 ring-4 ring-white dark:ring-slate-900' : ($maintenance->isRejected() ? 'bg-slate-300 dark:bg-slate-700' : 'bg-amber-400 ring-4 ring-white dark:ring-slate-900') }}"></span>
                            <div class="text-xs font-bold {{ in_array($maintenance->status->value, ['in_progress', 'resolved']) ? 'text-slate-900 dark:text-white' : 'text-slate-400' }}">
                                Dalam Penanganan
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $maintenance->isInProgress() || $maintenance->isResolved() ? 'Sedang/sudah ditangani teknisi' : 'Menunggu review tim pengelola' }}
                            </p>
                        </div>

                        <!-- Step 3: Resolved / Rejected -->
                        <div class="relative">
                            <span class="absolute -left-6 top-1 w-4 h-4 rounded-full {{ $maintenance->isResolved() ? 'bg-emerald-500' : ($maintenance->isRejected() ? 'bg-rose-500' : 'bg-slate-300 dark:bg-slate-700') }} ring-4 ring-white dark:ring-slate-900"></span>
                            <div class="text-xs font-bold {{ $maintenance->isResolved() ? 'text-emerald-600 dark:text-emerald-400' : ($maintenance->isRejected() ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400') }}">
                                {{ $maintenance->isRejected() ? 'Laporan Ditolak' : 'Selesai Diperbaiki' }}
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                {{ $maintenance->resolved_at ? $maintenance->resolved_at->format('d M Y, H:i') : ($maintenance->isRejected() ? 'Permintaan ditolak' : 'Menunggu pengerjaan selesai') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Room & Kost Info Card -->
                <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-3">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider">
                        Informasi Kamar
                    </h3>
                    <div class="text-xs space-y-2 text-slate-600 dark:text-slate-400">
                        <div class="flex justify-between">
                            <span>Nomor Kamar:</span>
                            <span class="font-bold text-slate-900 dark:text-white">Kamar {{ $maintenance->room?->room_number ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Lantai:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">Lantai {{ $maintenance->room?->floor ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tipe:</span>
                            <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $maintenance->room?->roomType?->name ?? 'Standard' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
