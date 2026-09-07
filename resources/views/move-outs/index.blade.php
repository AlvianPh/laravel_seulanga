<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Move-Out & Pengakhiran Kontrak') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @if (session('success'))
            <x-ui.alert type="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif
        @if (session('error'))
            <x-ui.alert type="error">
                {{ session('error') }}
            </x-ui.alert>
        @endif

        <!-- Quick Summary Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xs">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total</span>
                <span class="text-2xl font-extrabold text-gray-900 dark:text-white mt-1 block">{{ $counts['total'] }}</span>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-amber-200 dark:border-amber-900/50 shadow-xs">
                <span class="text-xs font-semibold text-amber-600 dark:text-amber-400 uppercase tracking-wider block">Menunggu</span>
                <span class="text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1 block">{{ $counts['pending'] }}</span>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-sky-200 dark:border-sky-900/50 shadow-xs">
                <span class="text-xs font-semibold text-sky-600 dark:text-sky-400 uppercase tracking-wider block">Disetujui</span>
                <span class="text-2xl font-extrabold text-sky-600 dark:text-sky-400 mt-1 block">{{ $counts['approved'] }}</span>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-indigo-200 dark:border-indigo-900/50 shadow-xs">
                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block">Inspeksi</span>
                <span class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400 mt-1 block">{{ $counts['inspection'] }}</span>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-emerald-200 dark:border-emerald-900/50 shadow-xs">
                <span class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider block">Selesai</span>
                <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 block">{{ $counts['completed'] }}</span>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-rose-200 dark:border-rose-900/50 shadow-xs">
                <span class="text-xs font-semibold text-rose-600 dark:text-rose-400 uppercase tracking-wider block">Ditolak</span>
                <span class="text-2xl font-extrabold text-rose-600 dark:text-rose-400 mt-1 block">{{ $counts['rejected'] }}</span>
            </div>
            <div class="p-4 rounded-xl bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-xs">
                <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Batal</span>
                <span class="text-2xl font-extrabold text-gray-500 dark:text-gray-400 mt-1 block">{{ $counts['cancelled'] }}</span>
            </div>
        </div>

        <x-ui.card>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Permohonan Keluar Kost</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola alur keluar kost, persetujuan, inspeksi kamar, dan settlement deposit.</p>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('move-outs.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                 placeholder="Cari nama penghuni, kamar, atau alasan..." class="text-sm" />
                </div>
                <div>
                    <x-ui.select name="status" class="text-sm">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </x-ui.select>
                </div>
                <div class="flex gap-2">
                    <x-ui.button type="submit" variant="primary" size="sm" class="flex-1">
                        Filter
                    </x-ui.button>
                    @if(request('search') || request('status'))
                        <x-ui.button href="{{ route('move-outs.index') }}" variant="secondary" size="sm">Reset</x-ui.button>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-700 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50/50 dark:bg-gray-800/50">
                            <th class="py-3 px-4">Penghuni & Kamar</th>
                            <th class="py-3 px-4">Tgl Rencana Keluar</th>
                            <th class="py-3 px-4">Alasan</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4">Waktu Pengajuan</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                        @forelse ($requests as $item)
                            <tr class="hover:bg-gray-50/80 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900 dark:text-white">{{ $item->tenant?->name ?? 'Anonim' }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Kamar {{ $item->contract?->room?->room_number ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4 text-xs font-semibold text-rose-600 dark:text-rose-400 whitespace-nowrap">
                                    {{ $item->requested_move_out_date->format('d M Y') }}
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-600 dark:text-gray-300 max-w-xs truncate">
                                    {{ $item->reason }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-ui.badge :status="$item->status">
                                        {{ $item->status->label() }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                                    {{ $item->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3 px-4 text-center whitespace-nowrap">
                                    <x-ui.button href="{{ route('move-outs.show', $item) }}" variant="secondary" size="xs">
                                        Kelola
                                    </x-ui.button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    Tidak ada data permohonan move-out yang sesuai.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($requests->hasPages())
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $requests->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
