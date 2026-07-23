<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kamar: ') . $room->room_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('rooms.update', $room) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Dasar -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Informasi Dasar</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Kamar <span class="text-red-500">*</span></label>
                                <input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('room_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lantai <span class="text-red-500">*</span></label>
                                <input type="number" name="floor" value="{{ old('floor', $room->floor) }}" min="1" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('floor') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe Kamar <span class="text-red-500">*</span></label>
                                <select name="room_type_id" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($roomTypes as $roomType)
                                        <option value="{{ $roomType->id }}" {{ old('room_type_id', $room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                            {{ $roomType->name }}
                                            @if ($roomType->default_price)
                                                (Ref: Rp {{ number_format($roomType->default_price, 0, ',', '.') }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_type_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Luas Kamar (m²) <span class="text-red-500">*</span></label>
                                <input type="number" step="0.01" name="size_m2" value="{{ old('size_m2', $room->size_m2) }}" min="0" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('size_m2') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Harga & Status -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Harga & Status</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Sewa / Bulan (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="monthly_price" value="{{ old('monthly_price', (int)$room->monthly_price) }}" min="0" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('monthly_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Deposit (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="deposit_price" value="{{ old('deposit_price', (int)$room->deposit_price) }}" min="0" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('deposit_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status Kamar <span class="text-red-500">*</span></label>
                                <select name="status" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->value }}" {{ old('status', $room->status->value) === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Fasilitas -->
                    <div class="mt-8 border-t pt-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Fasilitas Kamar</h3>
                        
                        @if ($facilities->isEmpty())
                            <p class="text-gray-400 italic text-sm mb-4">
                                Belum ada fasilitas tersedia.
                                <a href="{{ route('facilities.create') }}" class="text-indigo-600 hover:underline">Tambah fasilitas</a> terlebih dahulu.
                            </p>
                        @else
                            <div class="mb-6 grid grid-cols-2 md:grid-cols-4 gap-3">
                                @php
                                    $roomFacilityIds = old('facilities')
                                        ? array_map('intval', old('facilities'))
                                        : $room->facilities->pluck('id')->toArray();
                                @endphp
                                @foreach($facilities as $facility)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                               {{ in_array($facility->id, $roomFacilityIds) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $facility->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @endif
                        @error('facilities') <p class="text-red-500 text-sm mt-1 mb-4">{{ $message }}</p> @enderror

                        <!-- Form Tambah Foto Baru -->
                        <div class="mb-6 border border-gray-200 dark:border-gray-700 rounded p-4 bg-gray-50 dark:bg-gray-900">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tambah Foto Kamar (Max 5, @2MB)</label>
                            <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-600 dark:text-white bg-white">
                            @error('photos') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @error('photos.*') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                            Batal
                        </a>
                    </div>
                </form>

                <!-- Manajemen Foto Eksisting -->
                <div class="mt-12 border-t pt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Galeri Foto Eksisting</h3>
                    
                    @if($room->photos->isEmpty())
                        <p class="text-gray-500 italic">Belum ada foto untuk kamar ini.</p>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($room->photos as $photo)
                                <div class="border rounded overflow-hidden relative group">
                                    <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-32 object-cover" alt="Foto kamar">
                                    
                                    @if($photo->is_primary)
                                        <div class="absolute top-0 left-0 bg-indigo-600 text-white text-xs px-2 py-1 font-bold">
                                            PRIMARY
                                        </div>
                                    @endif

                                    <div class="p-2 flex flex-col gap-2 bg-gray-50 dark:bg-gray-700">
                                        @if(!$photo->is_primary)
                                            <form action="{{ route('rooms.photos.primary', [$room, $photo]) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="w-full text-xs bg-blue-100 text-blue-700 py-1 rounded hover:bg-blue-200">
                                                    Jadikan Primary
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <button type="button" 
                                                @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('rooms.photos.destroy', [$room, $photo]) }}', name: 'foto ini' })"
                                                class="w-full text-xs bg-red-100 text-red-700 py-1 rounded hover:bg-red-200">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
