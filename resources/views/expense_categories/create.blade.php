<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Kategori Pengeluaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                <form method="POST" action="{{ route('expense_categories.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Kategori Pengeluaran <span class="text-red-500">*</span>
                        </label>
                        <x-ui.input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                               placeholder="Contoh: AC, WiFi, Kasur, Kamar Mandi Dalam" />
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Ikon <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" name="description" value="{{ old('description') }}" maxlength="100"
                               class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Contoh: herodescription-ac, wifi, bed">
                        <p class="text-xs text-gray-400 mt-1">Nama ikon untuk ditampilkan di UI (referensi ikon dari sistem yang digunakan).</p>
                        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <x-ui.button type="submit">
                            Simpan
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
