<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kategori Pengeluaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <x-ui.card>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Daftar Kategori Pengeluaran</h3>
                        <x-ui.button href="{{ route('expense_categories.create') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Kategori
                        </x-ui.button>
                    </div>

                    @if ($expense_categories->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8 italic">Belum ada Kategori Pengeluaran.</p>
                    @else
                        <x-ui.table-wrapper>
                            <x-slot name="header">
                                <tr class="border-b dark:border-gray-700 text-left">
                                    <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Nama Kategori Pengeluaran</th>
                                    <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Ikon</th>
                                    <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Digunakan</th>
                                    <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                </tr>
                            </x-slot>
                                    @foreach ($expense_categories as $expense_category)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">
                                                {{ $expense_category->name }}
                                            </td>
                                            <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                                {{ $expense_category->description ?? '-' }}
                                            </td>
                                            <td class="py-3 pr-4 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $expense_category->expenses_count > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                    {{ $expense_category->expenses_count }} kamar
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <div class="flex items-center gap-2">
                                                    <x-ui.button size="sm" variant="warning" href="{{ route('expense_categories.edit', $expense_category) }}">
                                                        Edit
                                                    </x-ui.button>
                                                    <x-ui.button size="sm" variant="danger" type="button"
                                                            @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('expense_categories.destroy', $expense_category) }}', name: 'Kategori Pengeluaran {{ addslashes($expense_category->name) }}' })">
                                                        Hapus
                                                    </x-ui.button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                        </x-ui.table-wrapper>
                    @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
