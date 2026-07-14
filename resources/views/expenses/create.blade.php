<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Catat Pengeluaran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Tanggal Pengeluaran -->
                    <div class="mb-4">
                        <label for="expense_date" class="block font-medium text-gray-700 dark:text-gray-300">Tanggal Pengeluaran</label>
                        <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', date('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('expense_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Kategori -->
                    <div class="mb-4">
                        <label for="category" class="block font-medium text-gray-700 dark:text-gray-300">Kategori</label>
                        <select name="expense_category_id" id="expense_category_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($expenseCategories as $cat)
                                <option value="{{ $cat->id }}" {{ old('expense_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                        @error('expense_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="mb-4">
                        <label for="description" class="block font-medium text-gray-700 dark:text-gray-300">Deskripsi Singkat</label>
                        <input type="text" name="description" id="description" value="{{ old('description') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Contoh: Beli token listrik token 500rb, Bayar tukang AC...">
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Nominal -->
                    <div class="mb-4">
                        <label for="amount" class="block font-medium text-gray-700 dark:text-gray-300">Nominal (Rp)</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Contoh: 150000">
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload Struk -->
                    <div class="mb-6 border border-dashed border-gray-300 dark:border-gray-600 p-4 rounded bg-gray-50 dark:bg-gray-700">
                        <label for="receipt_photo" class="block font-medium text-gray-700 dark:text-gray-300">
                            Upload Foto Struk / Nota (Opsional)
                        </label>
                        <input type="file" name="receipt_photo" id="receipt_photo" accept="image/*"
                               class="mt-2 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-white file:text-gray-700 hover:file:bg-gray-100 dark:text-gray-300">
                        <p class="text-xs text-gray-500 mt-2">Maksimal ukuran file 2MB.</p>
                        @error('receipt_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <a href="{{ route('expenses.index') }}" class="text-gray-600 hover:text-gray-900 mr-4 text-sm">Batal</a>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700">
                            Simpan Pengeluaran
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
