<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                    Pengajuan Sewa Kamar
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Daftar permohonan sewa kamar dari calon penghuni yang masuk via Portal.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        <!-- Filter & Search -->
        <x-ui.card>
            <form method="GET" action="{{ route('tenant-applications.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Status Pengajuan</label>
                    <select name="status" class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu Review (Pending)</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Disetujui (Approved)</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Ditolak (Rejected)</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan (Cancelled)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Pencarian</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Nama / No. HP / Kamar..."
                           class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="flex items-center gap-2">
                    <x-ui.button type="submit" variant="primary" class="w-full">
                        Filter Data
                    </x-ui.button>
                    @if (request()->hasAny(['status', 'search']))
                        <a href="{{ route('tenant-applications.index') }}" class="py-2.5 px-3 text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white rounded-xl hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </x-ui.card>

        <!-- Applications Table -->
        <x-ui.card>
            @if ($applications->isEmpty())
                <x-ui.empty-state
                    title="Tidak Ada Pengajuan Ditemukan"
                    description="Belum ada pengajuan sewa kamar yang cocok dengan kriteria pencarian Anda."
                />
            @else
                <x-ui.table-wrapper>
                    <thead>
                        <tr>
                            <th>Pemohon</th>
                            <th>Kamar Pilihan</th>
                            <th>Tanggal Pengajuan</th>
                            <th>Status</th>
                            <th>Reviewer</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $app)
                            <tr>
                                <td>
                                    <div class="font-bold text-slate-900 dark:text-white">
                                        {{ $app->tenant?->name ?? 'Penghuni' }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $app->tenant?->phone }}
                                    </div>
                                </td>
                                <td>
                                    <div class="font-semibold text-slate-800 dark:text-slate-200">
                                        Kamar {{ $app->room?->room_number }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $app->room?->roomType?->name ?? 'Standard' }} • Lt. {{ $app->room?->floor }}
                                    </div>
                                </td>
                                <td class="text-xs text-slate-600 dark:text-slate-400">
                                    {{ $app->created_at->translatedFormat('d M Y, H:i') }}
                                </td>
                                <td>
                                    <x-ui.badge :variant="$app->status->badgeVariant()">
                                        {{ $app->status->label() }}
                                    </x-ui.badge>
                                </td>
                                <td class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $app->reviewer?->name ?: '-' }}
                                </td>
                                <td class="text-right">
                                    <a href="{{ route('tenant-applications.show', $app) }}"
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 transition-colors">
                                        Detail & Review
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-ui.table-wrapper>

                <div class="mt-4">
                    {{ $applications->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
