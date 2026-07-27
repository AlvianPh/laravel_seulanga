<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Tipe Kamar') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                <form method="POST" action="{{ route('room_types.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Tipe <span class="text-red-500">*</span>
                        </label>
                        <x-ui.input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                               placeholder="Contoh: Standard, Deluxe, Suite" />
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi
                        </label>
                        <x-ui.textarea name="description" rows="3" maxlength="500"
                                  placeholder="Deskripsi singkat tipe kamar ini (opsional)">{{ old('description') }}</x-ui.textarea>
                        @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Harga Rekomendasi (Rp)
                        </label>
                        <x-ui.input type="number" name="default_price" value="{{ old('default_price') }}" min="0" step="1000"
                               placeholder="Opsional — harga referensi untuk tipe ini" />
                        @error('default_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition-colors">
                            Simpan
                        </button>
                        <a href="{{ route('room_types.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors">
                            Batal
                        </a>
                    </div>
                </form>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
