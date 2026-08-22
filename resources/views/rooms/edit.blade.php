<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kamar: ') . $room->room_number }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>

            @if (session('success'))
                <x-ui.alert type="success">
                    {{ session('success') }}
                </x-ui.alert>
            @endif

            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Kamar: {{ $room->room_number }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui rincian tipe, ukuran, tarif sewa, status ketersediaan, dan galeri foto kamar</p>
            </div>

            <form method="POST" action="{{ route('rooms.update', $room) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Info Dasar -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Informasi Kamar</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nomor Kamar <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="room_number" value="{{ old('room_number', $room->room_number) }}" required class="text-sm" />
                            @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Lantai <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="floor" value="{{ old('floor', $room->floor) }}" min="1" required class="text-sm" />
                            @error('floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tipe Kamar <span class="text-red-500">*</span></label>
                            <x-ui.select name="room_type_id" required class="text-sm">
                                @foreach ($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ old('room_type_id', $room->room_type_id) == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }}
                                        @if ($roomType->default_price)
                                            (Ref: Rp {{ number_format($roomType->default_price, 0, ',', '.') }})
                                        @endif
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('room_type_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Luas Kamar (m²) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" step="0.01" name="size_m2" value="{{ old('size_m2', $room->size_m2) }}" min="0" required class="text-sm" />
                            @error('size_m2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Harga & Status -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Tarif Sewa & Status</h4>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Harga Sewa / Bulan (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="monthly_price" value="{{ old('monthly_price', (int)$room->monthly_price) }}" min="0" step="1000" required class="text-sm" />
                            @error('monthly_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Harga Deposit (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="deposit_price" value="{{ old('deposit_price', (int)$room->deposit_price) }}" min="0" step="1000" required class="text-sm" />
                            @error('deposit_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Status Kamar <span class="text-red-500">*</span></label>
                            <x-ui.select name="status" required class="text-sm">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status', $room->status->value) === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Fasilitas Kamar -->
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700/70">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4">Fasilitas Kamar</h4>

                    @if($facilities->isEmpty())
                        <p class="text-gray-400 italic text-xs mb-4">
                            Belum ada fasilitas tersedia.
                            <a href="{{ route('facilities.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Tambah fasilitas</a> terlebih dahulu.
                        </p>
                    @else
                        <div class="mb-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @php $currentFacilities = old('facilities', $room->facilities->pluck('id')->toArray()); @endphp
                            @foreach($facilities as $facility)
                                <label class="inline-flex items-center gap-2 p-2.5 rounded-xl border border-gray-200/80 dark:border-gray-700/70 hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer transition-colors">
                                    <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-xs focus:ring-indigo-500"
                                           {{ in_array($facility->id, array_map('intval', $currentFacilities)) ? 'checked' : '' }}>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $facility->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('facilities') <p class="text-red-500 text-xs mt-1 mb-4">{{ $message }}</p> @enderror

                    <!-- Upload Foto Tambahan -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tambah Foto Kamar (Max 5 total, @2MB)</label>
                        <input type="file" name="photos[]" multiple accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300">
                        @error('photos') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('photos.*') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Perubahan
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('rooms.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>

            <!-- Galeri Foto Saat Ini -->
            <div class="mt-8 border-t border-gray-100 dark:border-gray-700/70 pt-6">
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4">Galeri Foto Kamar Saat Ini</h4>

                @if($room->photos->isEmpty())
                    <p class="text-gray-400 italic text-xs">Belum ada foto untuk kamar ini.</p>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($room->photos as $photo)
                            <div class="relative group rounded-2xl overflow-hidden border border-gray-200/80 dark:border-gray-700 shadow-xs">
                                <img src="{{ Storage::url($photo->file_path) }}" alt="Foto Kamar" class="w-full h-32 object-cover">
                                
                                @if($photo->is_primary)
                                    <div class="absolute top-2 left-2 bg-indigo-600 text-white text-[10px] uppercase tracking-wider px-2 py-0.5 rounded-md font-bold shadow-xs">
                                        Sampul Utama
                                    </div>
                                @endif

                                <div class="p-2 flex flex-col gap-1.5 bg-gray-50/90 dark:bg-gray-800">
                                    @if(!$photo->is_primary)
                                        <form action="{{ route('rooms.photos.primary', [$room, $photo]) }}" method="POST">
                                            @csrf @method('PATCH')
                                            <x-ui.button size="sm" variant="secondary" type="submit" class="w-full text-xs">
                                                Jadikan Sampul
                                            </x-ui.button>
                                        </form>
                                    @endif
                                    
                                    <x-ui.button size="sm" variant="danger" type="button" 
                                            @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('rooms.photos.destroy', [$room, $photo]) }}', name: 'foto ini' })"
                                            class="w-full text-xs">
                                        Hapus Foto
                                    </x-ui.button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </x-ui.card>
    </div>
</x-app-layout>
