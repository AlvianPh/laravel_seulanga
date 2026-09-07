<x-portal-layout>
    <div class="space-y-6 max-w-3xl mx-auto">
        <!-- Back & Title -->
        <div>
            <a href="{{ route('portal.move-outs.index') }}"
               class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:text-slate-400 dark:hover:text-slate-200 transition-colors mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Daftar Move-Out
            </a>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                Ajukan Permohonan Keluar Kost
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                Kirimkan rencana tanggal keluar kost kepada pengelola untuk memulai tahapan review, inspeksi kondisi kamar, dan perhitungan settlement deposit.
            </p>
        </div>

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form action="{{ route('portal.move-outs.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Contract Info Snapshot -->
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-semibold">Kamar yang Ditempati</span>
                        <span class="text-sm font-bold text-slate-900 dark:text-white mt-0.5 inline-block">
                            Kamar {{ $activeContract->room->room_number }} (Lantai {{ $activeContract->room->floor }})
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-semibold">Periode Kontrak Aktif</span>
                        <span class="text-sm font-semibold text-slate-800 dark:text-slate-200 mt-0.5 inline-block">
                            {{ $activeContract->start_date->format('d M Y') }} s/d {{ $activeContract->end_date->format('d M Y') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-semibold">Nilai Deposit Terdaftar</span>
                        <span class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-0.5 inline-block">
                            Rp {{ number_format($activeContract->deposit_amount, 0, ',', '.') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-slate-500 dark:text-slate-400 uppercase tracking-wider block font-semibold">Status Kontrak</span>
                        <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300 inline-block mt-0.5">
                            {{ $activeContract->status->label() }}
                        </span>
                    </div>
                </div>

                <!-- Move Out Date -->
                <div>
                    <label for="requested_move_out_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Rencana Tanggal Keluar Kost <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="requested_move_out_date" id="requested_move_out_date" required
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('requested_move_out_date') }}"
                           class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-rose-500 focus:ring-rose-500">
                    <p class="text-[11px] text-slate-400 mt-1">Tanggal saat Anda berencana mengosongkan kamar dan menyerahkan kunci.</p>
                    @error('requested_move_out_date')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Alasan Keluar Kost <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason" id="reason" rows="3" required
                              placeholder="Contoh: Selesai masa kuliah / Pindah lokasi kerja / Kembali ke kampung halaman..."
                              class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-rose-500 focus:ring-rose-500">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                        Catatan Tambahan <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <textarea name="notes" id="notes" rows="3"
                              placeholder="Tuliskan nomor rekening pengembalian deposit jika ada, kontak darurat terbaru, atau pesan untuk pengelola..."
                              class="w-full rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-sm focus:border-rose-500 focus:ring-rose-500">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notice -->
                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900/50 text-xs text-amber-800 dark:text-amber-300 space-y-1">
                    <span class="font-bold block uppercase tracking-wider">Perhatian Penting:</span>
                    <p>1. Pengajuan move-out tidak serta-merta langsung mengakhiri kontrak Anda.</p>
                    <p>2. Pengelola akan menjadwalkan inspeksi kamar untuk memeriksa kondisi kelengkapan fasilitas.</p>
                    <p>3. Seluruh sisa tagihan yang belum lunas dan biaya perbaikan (jika ada kerusakan) akan diperhitungkan dengan nilai deposit Anda.</p>
                </div>

                <!-- Submit Buttons -->
                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <a href="{{ route('portal.move-outs.index') }}"
                       class="px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-sm shadow-md shadow-rose-500/20 transition-all hover:scale-[1.02] active:scale-[0.98]">
                        Kirim Permohonan Move-Out
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-portal-layout>
