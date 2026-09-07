<x-portal-layout>
    <div class="space-y-6 max-w-3xl mx-auto">
        <!-- Back & Title -->
        <div>
            <a href="{{ route('portal.permissions.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Izin
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Ajukan Permohonan Izin
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kirimkan permohonan izin kepada pengelola kost untuk tamu menginap, pulang larut malam, atau penggunaan alat elektronik.
            </p>
        </div>

        <!-- Form Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm"
             x-data="{
                selectedType: '{{ old('type', '') }}',
                requiresRange() {
                    return this.selectedType === 'guest_stay';
                },
                requiresStart() {
                    return this.selectedType === 'guest_stay' || this.selectedType === 'late_return';
                }
             }">
            <form action="{{ route('portal.permissions.store') }}" method="POST" class="space-y-6">
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

                <!-- Type -->
                <div>
                    <label for="type" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Jenis Permohonan Izin <span class="text-rose-500">*</span>
                    </label>
                    <select name="type" id="type" x-model="selectedType" required
                            class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Pilih Jenis Izin</option>
                        @foreach ($types as $type)
                            <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Judul Permohonan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" required
                           value="{{ old('title') }}"
                           placeholder="Contoh: Izin Menginapkan Teman Kuliah / Izin Lembur Kantor"
                           class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                    @error('title')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Dates Grid (Dynamic) -->
                <div x-show="requiresStart()" class="grid grid-cols-1 sm:grid-cols-2 gap-5" x-cloak>
                    <!-- Start Date -->
                    <div>
                        <label for="start_at" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal / Waktu Mulai <span x-show="requiresStart()" class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="start_at" id="start_at"
                               value="{{ old('start_at') }}"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('start_at')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div x-show="requiresRange()">
                        <label for="end_at" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            Tanggal / Waktu Selesai <span x-show="requiresRange()" class="text-rose-500">*</span>
                        </label>
                        <input type="datetime-local" name="end_at" id="end_at"
                               value="{{ old('end_at') }}"
                               class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">
                        @error('end_at')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Keterangan Detail <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="description" id="description" rows="4" required
                              placeholder="Tuliskan detail permohonan izin, nama tamu/identitas tamu jika menginap, spesifikasi perangkat elektronik, atau alasan terkait..."
                              class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('portal.permissions.index') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-sm shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Kirim Permohonan Izin
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-portal-layout>
