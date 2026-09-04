<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Breadcrumb / Back Link -->
        <div>
            <a href="{{ route('portal.rooms.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke Katalog Kamar
            </a>
        </div>

        <!-- Room Header & Photos -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                <div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300">
                        {{ $room->roomType?->name ?? 'Standard' }} • Lantai {{ $room->floor }}
                    </span>
                    <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white mt-2">
                        Kamar {{ $room->room_number }}
                    </h1>
                </div>
                <div class="text-right sm:text-right">
                    <div class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">
                        Rp {{ number_format($room->monthly_price, 0, ',', '.') }}
                    </div>
                    <span class="text-xs text-slate-500 dark:text-slate-400">Harga Sewa / Bulan</span>
                </div>
            </div>

            <!-- Photos Gallery -->
            <div class="mt-6">
                @if ($room->photos->isNotEmpty())
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach ($room->photos as $photo)
                            <div class="rounded-xl overflow-hidden h-48 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                                <img src="{{ Storage::url($photo->file_path) }}"
                                     alt="Foto Kamar {{ $room->room_number }}"
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-200">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center rounded-xl bg-slate-50 dark:bg-slate-800/60 text-slate-400 dark:text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm">Belum ada foto yang diunggah untuk kamar ini.</p>
                    </div>
                @endif
            </div>

            <!-- Key Specs & Deposit -->
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Luas Kamar</span>
                    <p class="text-base font-bold text-slate-900 dark:text-white mt-0.5">
                        {{ $room->size_m2 ? $room->size_m2 . ' m²' : '-' }}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Besaran Deposit</span>
                    <p class="text-base font-bold text-slate-900 dark:text-white mt-0.5">
                        Rp {{ number_format($room->deposit_price, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 col-span-2 sm:col-span-1">
                    <span class="text-xs text-slate-500 dark:text-slate-400">Status Ketersediaan</span>
                    <p class="text-base font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                        Siap Ditempati
                    </p>
                </div>
            </div>

            <!-- Facilities -->
            <div class="mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white mb-3">Fasilitas Kamar</h3>
                @if ($room->facilities->isNotEmpty())
                    <div class="flex flex-wrap gap-2">
                        @foreach ($room->facilities as $facility)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ $facility->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-slate-400">Fasilitas standar kost.</p>
                @endif
            </div>
        </div>

        <!-- Application Form Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Formulir Pengajuan Sewa Kamar</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                Kirim pengajuan kamar ini kepada pengelola kost. Pengajuan akan segera ditinjau oleh Admin/Owner.
            </p>

            @if ($canApply)
                <form method="POST" action="{{ route('portal.applications.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="room_id" value="{{ $room->id }}">

                    <div>
                        <x-ui.textarea
                            name="application_notes"
                            label="Catatan Pengajuan (Opsional)"
                            rows="3"
                            placeholder="Contoh: Rencana masuk tanggal 15 bulan ini, membawa kendaraan bermotor, dsb."
                            :value="old('application_notes')"
                        />
                    </div>

                    <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200 text-xs leading-relaxed">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-blue-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>
                                <strong>Pemberitahuan:</strong> Pengajuan ini merupakan permohonan minat sewa. Kontrak resmi dan petunjuk pembayaran awal akan diterbitkan setelah pengajuan disetujui oleh pengelola.
                            </span>
                        </div>
                    </div>

                    <div class="pt-2">
                        <x-ui.button type="submit" variant="primary" class="w-full sm:w-auto">
                            Kirim Pengajuan Kamar Ini
                        </x-ui.button>
                    </div>
                </form>
            @else
                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm">
                    @if (auth()->user()->tenant?->activeContract())
                        <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-medium">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <span>Anda sudah memiliki kontrak kamar yang sedang aktif.</span>
                        </div>
                    @elseif (auth()->user()->tenant?->pendingApplication())
                        <div class="flex items-center gap-2 text-amber-700 dark:text-amber-400 font-medium">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Anda masih memiliki pengajuan sewa lain yang sedang menunggu review.</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-400 font-medium">
                            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Kamar ini sedang tidak dapat diajukan.</span>
                        </div>
                    @endif
                    <div class="mt-3">
                        <a href="{{ route('portal.applications.index') }}" class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                            Lihat Riwayat Pengajuan Anda &rarr;
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-portal-layout>
