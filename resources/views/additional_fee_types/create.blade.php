<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Jenis Denda/Biaya') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>
                    <form action="{{ route('additional_fee_types.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="nama" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                            <x-ui.input type="text" name="nama" id="nama" value="{{ old('nama') }}" required />
                            @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="jenis" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Jenis</label>
                            <x-ui.select name="jenis" id="jenis" required>
                                <option value="nominal_tetap" {{ old('jenis') == 'nominal_tetap' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                                <option value="persentase" {{ old('jenis') == 'persentase' ? 'selected' : '' }}>Persentase (%)</option>
                            </x-ui.select>
                            @error('jenis')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="nilai_default" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nilai Default</label>
                            <x-ui.input type="number" step="0.01" min="0" name="nilai_default" id="nilai_default" value="{{ old('nilai_default', '0') }}" required />
                            @error('nilai_default')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-6 flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Aktif</label>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <x-ui.button variant="secondary" href="{{ route('additional_fee_types.index') }}">Batal</x-ui.button>
                            <x-ui.button type="submit">Simpan</x-ui.button>
                        </div>
                    </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
