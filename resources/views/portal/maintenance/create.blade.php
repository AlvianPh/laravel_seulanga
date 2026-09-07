<x-portal-layout>
    <div class="space-y-6 max-w-3xl mx-auto">
        <!-- Back & Title -->
        <div>
            <a href="{{ route('portal.maintenance.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Perbaikan
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Lapor Kerusakan Kamar
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Isi rincian kendala atau kerusakan pada kamar Anda agar pengelola dapat segera menindaklanjuti.
            </p>
        </div>

        <!-- Form Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form action="{{ route('portal.maintenance.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Room info (read-only) -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider block">Kamar Anda</span>
                        <span class="text-base font-bold text-slate-900 dark:text-white">
                            Kamar {{ $activeContract->room->room_number }} (Lantai {{ $activeContract->room->floor }})
                        </span>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-lg bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                        Kontrak Aktif
                    </span>
                </div>

                <!-- Category & Priority Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Kategori Kerusakan <span class="text-rose-500">*</span>
                        </label>
                        <select name="category" id="category" required
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->value }}" {{ old('category') === $cat->value ? 'selected' : '' }}>
                                    {{ $cat->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('category')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tingkat Urgensi / Prioritas <span class="text-rose-500">*</span>
                        </label>
                        <select name="priority" id="priority" required
                                class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                            <option value="">Pilih Prioritas</option>
                            @foreach ($priorities as $pri)
                                <option value="{{ $pri->value }}" {{ (old('priority', 'medium') === $pri->value) ? 'selected' : '' }}>
                                    {{ $pri->label() }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Location -->
                <div>
                    <label for="location" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Lokasi / Bagian yang Rusak <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="location" id="location" required
                           value="{{ old('location') }}"
                           placeholder="Contoh: Kamar Mandi (Kran Bocor), Stopkontak Meja Belajar, AC Kamar"
                           class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('location')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Deskripsi Keluhan Detail <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="4" required
                              placeholder="Jelaskan secara rinci kendala yang dialami, sejak kapan terjadi, dan gejala kerusakan..."
                              class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Photo Upload -->
                <div x-data="{ photoName: null, photoPreview: null }">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Foto Bukti Kerusakan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 dark:border-slate-700 border-dashed rounded-xl hover:border-emerald-500 transition-colors">
                        <div class="space-y-2 text-center">
                            <!-- Image preview -->
                            <div x-show="photoPreview" class="mb-3">
                                <img :src="photoPreview" class="max-h-48 rounded-lg mx-auto object-cover border border-slate-200 dark:border-slate-700">
                            </div>

                            <div x-show="!photoPreview">
                                <svg class="mx-auto h-12 w-12 text-slate-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                            <div class="flex text-sm text-slate-600 dark:text-slate-400 justify-center">
                                <label for="photo" class="relative cursor-pointer rounded-md font-semibold text-emerald-600 dark:text-emerald-400 hover:underline focus-within:outline-none">
                                    <span x-text="photoName ? 'Ganti Foto' : 'Unggah Foto Kerusakan'"></span>
                                    <input id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/jpg,image/webp" class="sr-only"
                                           @change="
                                                photoName = $event.target.files[0].name;
                                                const reader = new FileReader();
                                                reader.onload = (e) => { photoPreview = e.target.result; };
                                                reader.readAsDataURL($event.target.files[0]);
                                           ">
                                </label>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                Format: JPG, JPEG, PNG, WEBP (Maksimal 5MB)
                            </p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('portal.maintenance.index') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Kirim Laporan Perbaikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-portal-layout>
