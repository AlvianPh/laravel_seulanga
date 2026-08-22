<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengeluaran Operasional') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <x-ui.card>

                @if (session('success'))
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pengeluaran Operasional</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Catat dan pantau seluruh beban biaya operasional kost</p>
                    </div>
                    
                    <x-ui.button href="{{ route('expenses.create') }}" variant="primary" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Catat Pengeluaran
                    </x-ui.button>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('expenses.index') }}" class="mb-6 bg-gray-50/80 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-700/70" x-data x-ref="form">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Cari Keterangan</label>
                            <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari deskripsi pengeluaran..."
                                   class="text-sm" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Kategori</label>
                            <x-ui.select name="category_id" @change="$refs.form.submit()" class="text-sm">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Dari Tanggal</label>
                            <x-ui.input type="date" name="start_date" value="{{ request('start_date') }}" @change="$refs.form.submit()"
                                   class="text-sm" />
                        </div>

                        <div class="flex items-end gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Sampai Tanggal</label>
                                <x-ui.input type="date" name="end_date" value="{{ request('end_date') }}" @change="$refs.form.submit()"
                                       class="text-sm" />
                            </div>
                            <x-ui.button type="submit" variant="primary" size="sm">
                                Cari
                            </x-ui.button>
                            @if(request()->anyFilled(['search', 'category_id', 'start_date', 'end_date']))
                                <x-ui.button href="{{ route('expenses.index') }}" variant="secondary" size="sm">Reset</x-ui.button>
                            @endif
                        </div>

                    </div>
                </form>

                <!-- Table -->
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Tgl Keluar</th>
                            <th class="px-4 py-3.5">Kategori</th>
                            <th class="px-4 py-3.5">Keterangan</th>
                            <th class="px-4 py-3.5">Nominal (Rp)</th>
                            <th class="px-4 py-3.5">Diinput Oleh</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                            @forelse ($expenses as $expense)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3.5 text-xs font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">{{ $expense->expense_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                            {{ $expense->expenseCategory->name }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3.5 max-w-xs truncate text-gray-900 dark:text-white" title="{{ $expense->description }}">{{ $expense->description }}</td>
                                    <td class="px-4 py-3.5 font-bold text-rose-600 dark:text-rose-400">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5 text-xs text-gray-500 dark:text-gray-400">{{ $expense->creator->name ?? 'Dihapus' }}</td>
                                    <td class="px-4 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('expenses.show', $expense) }}">Detail</x-ui.button>
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('expenses.edit', $expense) }}">Edit</x-ui.button>
                                        <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('expenses.destroy', $expense) }}', name: 'data pengeluaran ini' })">Hapus</x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data pengeluaran operasional.
                                    </td>
                                </tr>
                            @endforelse
                </x-ui.table-wrapper>

                <div class="mt-6">
                    {{ $expenses->links() }}
                </div>

        </x-ui.card>
    </div>
</x-app-layout>
