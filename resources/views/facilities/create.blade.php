<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                <form method="POST" action="{{ route('facilities.store') }}">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Nama Fasilitas <span class="text-red-500">*</span>
                        </label>
                        <x-ui.input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                               placeholder="Contoh: AC, WiFi, Kasur, Kamar Mandi Dalam" />
                        @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Ikon <span class="text-gray-400 font-normal">(opsional)</span>
                        </label>
                        <input type="text" name="icon" value="{{ old('icon') }}" maxlength="100"
                               class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                               placeholder="Contoh: heroicon-ac, wifi, bed">
                        <p class="text-xs text-gray-400 mt-1">Nama ikon untuk ditampilkan di UI (referensi ikon dari sistem yang digunakan).</p>
                        @error('icon') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex gap-3">
                        <x-ui.button type="submit">
                            Simpan
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('facilities.index') }}">
                            Batal
                        </x-ui.button>
                    </div>
                </form>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
