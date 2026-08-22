<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Fasilitas') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Fasilitas</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tambahkan fasilitas kamar atau fasilitas bersama</p>
            </div>

            <form method="POST" action="{{ route('facilities.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Nama Fasilitas <span class="text-red-500">*</span>
                    </label>
                    <x-ui.input type="text" name="name" value="{{ old('name') }}" required maxlength="100" class="text-sm"
                           placeholder="Contoh: AC, WiFi, Kamar Mandi Dalam, Meja Belajar" />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Ikon <span class="text-gray-400 font-normal lowercase">(opsional)</span>
                    </label>
                    <x-ui.input type="text" name="icon" value="{{ old('icon') }}" maxlength="100" class="text-sm"
                           placeholder="Contoh: wifi, tv, air-conditioner" />
                    <p class="text-[11px] text-gray-400 mt-1">Nama alias ikon untuk penanda visual fasilitas.</p>
                    @error('icon') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Fasilitas
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('facilities.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
