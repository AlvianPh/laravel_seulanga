<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengeluaran Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                    @if (session('success'))
                        <x-ui.alert type="success">
                            {{ session('success') }}
                        </x-ui.alert>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Pengeluaran</h3>
                        
                        <x-ui.button href="{{ route('expenses.create') }}">
                            + Catat Pengeluaran
                        </x-ui.button>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('expenses.index') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" x-data x-ref="form">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            
                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-500 mb-1">Cari Keterangan</label>
                                <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Cari deskripsi..."
                                       class="text-sm" />
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                                <x-ui.select name="category_id" @change="$refs.form.submit()" class="text-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </x-ui.select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                                <x-ui.input type="date" name="start_date" value="{{ request('start_date') }}" @change="$refs.form.submit()"
                                       class="text-sm" />
                            </div>

                            <div class="md:col-span-1 flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                                    <x-ui.input type="date" name="end_date" value="{{ request('end_date') }}" @change="$refs.form.submit()"
                                           class="text-sm" />
                                </div>
                                <x-ui.button type="submit" variant="secondary">
                                    Cari
                                </x-ui.button>
                                @if(request()->anyFilled(['search', 'category', 'start_date', 'end_date']))
                                    <x-ui.button href="{{ route('expenses.index') }}" variant="secondary">Reset</x-ui.button>
                                @endif
                            </div>

                        </div>
                    </form>

                    <!-- Table -->
                    <x-ui.table-wrapper>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3">Tgl Keluar</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Keterangan</th>
                                <th class="px-4 py-3">Nominal (Rp)</th>
                                <th class="px-4 py-3">Input Oleh</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </x-slot>
                                @forelse ($expenses as $expense)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-semibold">
                                                {{ $expense->expenseCategory->name }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 max-w-xs truncate" title="{{ $expense->description }}">{{ $expense->description }}</td>
                                        <td class="px-4 py-3 font-bold text-red-600">{{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-xs text-gray-500">{{ $expense->creator->name ?? 'Dihapus' }}</td>
                                        <td class="px-4 py-3 space-x-2 whitespace-nowrap">
                                            <x-ui.button size="sm" variant="secondary" href="{{ route('expenses.show', $expense) }}">Detail</x-ui.button>
                                            <x-ui.button size="sm" variant="warning" href="{{ route('expenses.edit', $expense) }}">Edit</x-ui.button>
                                            <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('expenses.destroy', $expense) }}', name: 'data pengeluaran ini' })">Hapus</x-ui.button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data pengeluaran operasional.
                                        </td>
                                    </tr>
                                @endforelse
                    </x-ui.table-wrapper>

                    <div class="mt-4">
                        {{ $expenses->links() }}
                    </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
