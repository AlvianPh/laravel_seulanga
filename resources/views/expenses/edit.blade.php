<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Pengeluaran') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Catatan Pengeluaran</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui rincian kategori, nominal biaya, atau lampiran nota bukti</p>
            </div>

            <form method="POST" action="{{ route('expenses.update', $expense) }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @method('PATCH')

                <!-- Tanggal Pengeluaran -->
                <div>
                    <label for="expense_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Transaksi Pengeluaran <span class="text-red-500">*</span></label>
                    <x-ui.input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required class="text-sm" />
                    @error('expense_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Kategori -->
                <div>
                    <label for="expense_category_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Kategori Pengeluaran <span class="text-red-500">*</span></label>
                    <x-ui.select name="expense_category_id" id="expense_category_id" required class="text-sm">
                        @foreach(($categories ?? $expenseCategories ?? []) as $cat)
                            <option value="{{ $cat->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </x-ui.select>
                    @error('expense_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Keterangan -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Deskripsi / Keterangan Pengeluaran <span class="text-red-500">*</span></label>
                    <x-ui.input type="text" name="description" id="description" value="{{ old('description', $expense->description) }}" required class="text-sm" />
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Nominal -->
                <div>
                    <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nominal Pengeluaran (Rp) <span class="text-red-500">*</span></label>
                    <x-ui.input type="number" name="amount" id="amount" value="{{ old('amount', (int)$expense->amount) }}" required min="1" step="1000" class="text-sm" />
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Upload Struk -->
                <div class="border border-dashed border-gray-200 dark:border-gray-700 p-4 rounded-2xl bg-gray-50/50 dark:bg-gray-800/40">
                    <label for="receipt_photo" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Ganti Foto Struk / Nota (Opsional)
                    </label>
                    @if($expense->receipt_path)
                        <div class="mb-3 p-3 bg-indigo-50/70 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60 rounded-xl flex justify-between items-center text-xs">
                            <span class="text-indigo-800 dark:text-indigo-200 font-medium">Sudah memiliki lampiran foto struk.</span>
                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="text-indigo-600 dark:text-indigo-400 font-bold hover:underline">Lihat Lampiran</a>
                        </div>
                    @endif
                    <input type="file" name="receipt_photo" id="receipt_photo" accept="image/*"
                           class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300">
                    <p class="text-[11px] text-gray-400 mt-2">Pilih file baru hanya jika ingin mengganti struk yang sudah ada.</p>
                    @error('receipt_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Perbarui Pengeluaran
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('expenses.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>

        </x-ui.card>
    </div>
</x-app-layout>
