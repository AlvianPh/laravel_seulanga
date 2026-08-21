<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kategori Pengeluaran: ') . $expense_category->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                <form method="POST" action="{{ route('expense_categories.update', $expense_category) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Kategori Pengeluaran <span class="text-red-500">*</span>
                        </label>
                        <x-ui.input type="text" name="name" value="{{ old('name', $expense_category->name) }}" required maxlength="100" />
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Ikon <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" name="description" value="{{ old('description', $expense_category->description) }}" maxlength="100"
                               class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <x-ui.button type="submit">
                            Simpan Perubahan
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('expense_categories.index') }}">
                            Batal
                        </x-ui.button>
                    </div>
                </form>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
