<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Kamar Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Kamar Kost Baru</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftarkan unit kamar baru beserta tipe, lantai, ukuran, tarif sewa, deposit dan fasilitasnya</p>
            </div>

            <form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Info Dasar -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Informasi Kamar</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nomor / Nama Kamar <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="room_number" value="{{ old('room_number') }}" required class="text-sm" placeholder="Contoh: 101, A-02" />
                            @error('room_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Posisi Lantai <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="floor" value="{{ old('floor', 1) }}" min="1" required class="text-sm" placeholder="Lantai ke (contoh: 1)" />
                            @error('floor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tipe Kamar <span class="text-red-500">*</span></label>
                            <x-ui.select name="room_type_id" required class="text-sm">
                                <option value="" disabled selected>-- Pilih Tipe Kamar --</option>
                                @foreach ($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
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
                            <x-ui.input type="number" step="0.01" name="size_m2" value="{{ old('size_m2') }}" min="0" required class="text-sm" placeholder="Contoh: 12.5" />
                            @error('size_m2') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Harga & Status -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Tarif Sewa & Status</h4>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Harga Sewa / Bulan (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="monthly_price" value="{{ old('monthly_price') }}" min="0" step="1000" required class="text-sm" placeholder="Contoh: 1500000" />
                            @error('monthly_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Uang Jaminan / Deposit (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="deposit_price" value="{{ old('deposit_price', 0) }}" min="0" step="1000" required class="text-sm" placeholder="Contoh: 500000" />
                            @error('deposit_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Status Awal Kamar <span class="text-red-500">*</span></label>
                            <x-ui.select name="status" required class="text-sm">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ old('status', 'available') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Fasilitas & Foto -->
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700/70">
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4">Fasilitas Kamar</h4>

                    @if($facilities->isEmpty())
                        <p class="text-gray-400 italic text-xs mb-4">
                            Belum ada fasilitas terdaftar.
                            <a href="{{ route('facilities.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">Tambah fasilitas</a> terlebih dahulu.
                        </p>
                    @else
                        <div class="mb-6 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                            @php $oldFacilities = old('facilities', []); @endphp
                            @foreach($facilities as $facility)
                                <label class="inline-flex items-center gap-2 p-2.5 rounded-xl border border-gray-200/80 dark:border-gray-700/70 hover:bg-gray-50 dark:hover:bg-gray-700/40 cursor-pointer transition-colors">
                                    <input type="checkbox" name="facilities[]" value="{{ $facility->id }}"
                                           class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-xs focus:ring-indigo-500"
                                           {{ in_array($facility->id, array_map('intval', $oldFacilities)) ? 'checked' : '' }}>
                                    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $facility->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('facilities') <p class="text-red-500 text-xs mt-1 mb-4">{{ $message }}</p> @enderror

                    <div x-data="{
                        previewUrls: [],
                        handleFilesChange(event) {
                            this.previewUrls = [];
                            const files = Array.from(event.target.files);
                            files.forEach((file) => {
                                if (file && file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => {
                                        this.previewUrls.push(e.target.result);
                                    };
                                    reader.readAsDataURL(file);
                                }
                            });
                        }
                    }" class="mb-6 p-4 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Foto Kamar (Maksimal 5 foto, @2MB)</label>
                        
                        <!-- Client-side Multiple Preview -->
                        <div x-show="previewUrls.length > 0" x-cloak class="mb-4">
                            <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 block mb-2">
                                Preview Foto Baru (<span x-text="previewUrls.length"></span> foto dipilih):
                            </span>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                                <template x-for="(url, idx) in previewUrls" :key="idx">
                                    <div class="relative group rounded-xl overflow-hidden border-2 border-indigo-500/80 shadow-xs">
                                        <img :src="url" alt="Preview Kamar" class="w-full h-24 object-cover">
                                        <div class="absolute bottom-1 left-1 bg-gray-900/80 text-white text-[9px] font-bold px-1.5 py-0.5 rounded" x-text="idx === 0 ? 'Sampul' : '#' + (idx + 1)"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <input type="file" name="photos[]" multiple @change="handleFilesChange($event)" accept="image/jpeg,image/png,image/webp"
                               class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 cursor-pointer">
                        <p class="text-[11px] text-gray-400 mt-1.5">Foto pertama yang diupload akan otomatis menjadi sampul (thumbnail) utama kamar.</p>
                        @error('photos') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @error('photos.*') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Kamar
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('rooms.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
