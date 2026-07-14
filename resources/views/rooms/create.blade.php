<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Kamar Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Dasar -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Informasi Dasar</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Kamar <span class="text-red-500">*</span></label>
                                <input type="text" name="room_number" value="{{ old('room_number') }}" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('room_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lantai <span class="text-red-500">*</span></label>
                                <input type="number" name="floor" value="{{ old('floor') }}" min="1" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('floor') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Kamar <span class="text-red-500">*</span></label>
                                <select name="type" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="" disabled selected>Pilih Tipe</option>
                                    @foreach ($types as $type)
                                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Luas Kamar (m2) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="size_m2" value="{{ old('size_m2') }}" min="0" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('size_m2') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Harga & Status -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Harga & Status</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Sewa / Bulan (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="monthly_price" value="{{ old('monthly_price') }}" min="0" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('monthly_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Deposit (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="deposit_price" value="{{ old('deposit_price') }}" min="0" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('deposit_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Kamar <span class="text-red-500">*</span></label>
                                <select name="status" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" {{ old('status') === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Fasilitas & Foto -->
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Fasilitas Tambahan</h3>
                        
                        <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $commonFacilities = ['AC', 'Kipas Angin', 'Kamar Mandi Dalam', 'Kamar Mandi Luar', 'Kasur', 'Lemari Pakaian', 'Meja Belajar', 'Kursi', 'WiFi', 'Jendela Luar'];
                                $oldFacilities = old('facilities', []);
                            @endphp
                            
                            @foreach($commonFacilities as $fac)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="facilities[]" value="{{ $fac }}"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           {{ in_array($fac, $oldFacilities) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $fac }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('facilities') <p class="text-red-500 text-sm mt-1 mb-4">{{ $message }}</p> @enderror

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Upload Foto Kamar (Max 5, @2MB)</label>
                            <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p class="text-xs text-gray-500 mt-1">Foto pertama yang dipilih akan menjadi foto utama (thumbnail).</p>
                            @error('photos') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @error('photos.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Simpan Kamar
                        </button>
                        <a href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
