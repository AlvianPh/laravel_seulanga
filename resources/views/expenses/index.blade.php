<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengeluaran Operasional') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Pengeluaran</h3>
                        
                        <a href="{{ route('expenses.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700">
                            + Catat Pengeluaran
                        </a>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('expenses.index') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg" x-data x-ref="form">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            
                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-500 mb-1">Cari Keterangan</label>
                                <input type="text" name="search" value="{{ request('search') }}"
                                       placeholder="Cari deskripsi..."
                                       class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-500 mb-1">Kategori</label>
                                <select name="category_id" @change="$refs.form.submit()" class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                    <option value="">Semua Kategori</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date') }}" @change="$refs.form.submit()"
                                       class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                            </div>

                            <div class="md:col-span-1 flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" @change="$refs.form.submit()"
                                           class="w-full border rounded px-3 py-2 text-sm dark:bg-gray-800 dark:border-gray-600 dark:text-white">
                                </div>
                                <button type="submit" class="px-3 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 dark:bg-gray-600 dark:text-gray-200">
                                    Cari
                                </button>
                                @if(request()->anyFilled(['search', 'category', 'start_date', 'end_date']))
                                    <a href="{{ route('expenses.index') }}" class="px-3 py-2 text-sm text-red-600 hover:underline">Reset</a>
                                @endif
                            </div>

                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                                <tr>
                                    <th class="px-4 py-3">Tgl Keluar</th>
                                    <th class="px-4 py-3">Kategori</th>
                                    <th class="px-4 py-3">Keterangan</th>
                                    <th class="px-4 py-3">Nominal (Rp)</th>
                                    <th class="px-4 py-3">Input Oleh</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                            <a href="{{ route('expenses.show', $expense) }}" class="text-blue-600 hover:underline">Detail</a>
                                            <a href="{{ route('expenses.edit', $expense) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data pengeluaran ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data pengeluaran operasional.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $expenses->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
