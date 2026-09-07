<x-portal-layout>
    <div class="space-y-6 max-w-2xl mx-auto">
        <!-- Header & Breadcrumb -->
        <div>
            <a href="{{ route('portal.documents.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Dokumen
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Unggah Dokumen Baru
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Unggah berkas identitas atau dokumen pendukung untuk keperluan verifikasi pengelola kost.
            </p>
        </div>

        <!-- Form Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="POST" action="{{ route('portal.documents.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Judul Dokumen <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="Contoh: Foto KTP Asli / Kartu Mahasiswa / Surat Kerja"
                           class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    @error('title')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Jenis Dokumen <span class="text-rose-500">*</span>
                    </label>
                    <select id="type" name="type" required
                            class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                        <option value="">-- Pilih Jenis Dokumen --</option>
                        @foreach ($allowedTypes as $tp)
                            <option value="{{ $tp->value }}" {{ old('type') === $tp->value ? 'selected' : '' }}>
                                {{ $tp->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- File Upload -->
                <div>
                    <label for="file" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Pilih Berkas Dokumen <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" id="file" name="file" required accept=".pdf,.jpg,.jpeg,.png"
                           class="w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-950/60 dark:file:text-emerald-400 border border-slate-300 dark:border-slate-700 rounded-xl p-1 bg-slate-50 dark:bg-slate-800/50">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1.5">
                        Format yang didukung: PDF, JPG, JPEG, atau PNG (Maksimal 5MB).
                    </p>
                    @error('file')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Keterangan Tambahan (Opsional)
                    </label>
                    <textarea id="description" name="description" rows="3" placeholder="Catatan tambahan mengenai dokumen ini..."
                              class="w-full text-sm rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-white px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('portal.documents.index') }}"
                       class="px-4 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-semibold text-xs transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Unggah Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-portal-layout>
