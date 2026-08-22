<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Tipe Kamar') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Tipe Kamar</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tambahkan kategori atau klasifikasi kamar baru</p>
            </div>

            <form method="POST" action="{{ route('room_types.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Nama Tipe <span class="text-red-500">*</span>
                    </label>
                    <x-ui.input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                           placeholder="Contoh: Standard, Deluxe, VIP" class="text-sm" />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Deskripsi
                    </label>
                    <x-ui.textarea name="description" rows="3" maxlength="500" class="text-sm"
                              placeholder="Deskripsi singkat tipe kamar ini (opsional)">{{ old('description') }}</x-ui.textarea>
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Harga Rekomendasi (Rp)
                    </label>
                    <x-ui.input type="number" name="default_price" value="{{ old('default_price') }}" min="0" step="1000" class="text-sm"
                           placeholder="Opsional — harga referensi untuk tipe ini" />
                    @error('default_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Tipe Kamar
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('room_types.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
