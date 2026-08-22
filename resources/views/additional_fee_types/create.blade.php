<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Jenis Denda/Biaya') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Jenis Biaya / Denda</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftarkan konfigurasi denda telat bayar atau biaya tambahan (kebersihan, listrik ekstra, dll)</p>
            </div>

            <form action="{{ route('additional_fee_types.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Biaya / Denda <span class="text-red-500">*</span></label>
                    <x-ui.input type="text" name="nama" id="nama" value="{{ old('nama') }}" required class="text-sm" placeholder="Contoh: Denda Keterlambatan, Biaya Listrik AC Ekstra" />
                    @error('nama')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="jenis" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Jenis Perhitungan <span class="text-red-500">*</span></label>
                    <x-ui.select name="jenis" id="jenis" required class="text-sm">
                        <option value="nominal_tetap" {{ old('jenis') == 'nominal_tetap' ? 'selected' : '' }}>Nominal Tetap (Rp)</option>
                        <option value="persentase" {{ old('jenis') == 'persentase' ? 'selected' : '' }}>Persentase (% dari total sewa)</option>
                    </x-ui.select>
                    @error('jenis')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="nilai_default" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nilai Default <span class="text-red-500">*</span></label>
                    <x-ui.input type="number" step="0.01" min="0" name="nilai_default" id="nilai_default" value="{{ old('nilai_default', '0') }}" required class="text-sm" placeholder="Contoh: 50000 atau 5" />
                    @error('nilai_default')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-xs focus:ring-indigo-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 block text-xs font-medium text-gray-700 dark:text-gray-300">Status Aktif (Tersedia untuk digunakan)</label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">Simpan Jenis Biaya</x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('additional_fee_types.index') }}">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
