<x-portal-layout>
    <div class="space-y-6">
        <!-- Welcome Banner -->
        <div class="p-6 md:p-8 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 text-white shadow-lg relative overflow-hidden">
            <div class="relative z-10 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-emerald-100 text-xs font-semibold mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                    Portal Penghuni Seulanga
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                    Halo, {{ $user->name }} 👋
                </h1>
                <p class="mt-2 text-emerald-100 text-sm sm:text-base leading-relaxed">
                    Selamat datang di portal mandiri Anda. Di sini Anda dapat memantau status hunian, tagihan sewa, dan pengajuan layanan kost.
                </p>
            </div>
            
            <!-- Background Decorative circles -->
            <div class="absolute -right-8 -bottom-8 w-48 h-48 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
            <div class="absolute right-1/4 -top-8 w-32 h-32 rounded-full bg-emerald-400/20 blur-xl pointer-events-none"></div>
        </div>

        @if (!$tenant)
            <!-- Unlinked Tenant State -->
            <div class="p-6 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-900 dark:text-amber-200 shadow-sm">
                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-900/60 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-base font-bold text-amber-900 dark:text-amber-200">Akun Belum Terhubung dengan Data Penghuni</h2>
                        <p class="mt-1 text-sm text-amber-700 dark:text-amber-300">
                            Akun login Anda belum dikaitkan ke data penghuni kamar. Silakan hubungi pengelola kost (Admin/Owner) agar data identitas dan kontrak kamar Anda segera diaktifkan.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- Summary Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Status Kamar Card -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kamar Ditempati</span>
                            <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </span>
                        </div>
                        @if ($currentRoom)
                            <div class="text-2xl font-bold text-slate-900 dark:text-white">
                                Kamar {{ $currentRoom->room_number }}
                            </div>
                            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                                Lantai {{ $currentRoom->floor }} • {{ $currentRoom->roomType?->name ?? 'Standard' }}
                            </p>
                        @else
                            <div class="text-lg font-bold text-slate-700 dark:text-slate-300">
                                Belum Ada Kamar Aktif
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Kontrak sewa kamar Anda belum dimulai atau sudah berakhir.
                            </p>
                        @endif
                    </div>
                    @if ($activeContract)
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
                            <span>Sewa: Rp {{ number_format($activeContract->rent_price, 0, ',', '.') }}/bln</span>
                            <span class="text-emerald-600 dark:text-emerald-400 font-semibold">Kontrak Aktif</span>
                        </div>
                    @endif
                </div>

                <!-- Tagihan Aktif Card -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tagihan Berjalan</span>
                            <span class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </span>
                        </div>
                        @if ($pendingInvoice)
                            <div class="text-2xl font-bold text-slate-900 dark:text-white">
                                Rp {{ number_format($pendingInvoice->total_amount, 0, ',', '.') }}
                            </div>
                            <p class="mt-1 text-sm text-amber-600 dark:text-amber-400 font-medium">
                                Jatuh Tempo: {{ \Carbon\Carbon::parse($pendingInvoice->due_date)->format('d M Y') }}
                            </p>
                        @else
                            <div class="text-lg font-bold text-emerald-600 dark:text-emerald-400">
                                Tidak Ada Tunggakan
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Semua tagihan Anda telah lunas.
                            </p>
                        @endif
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        <a href="{{ route('portal.invoices.index') }}" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                            Lihat Semua Tagihan &rarr;
                        </a>
                    </div>
                </div>

                <!-- Informasi Akun Penghuni Card -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between sm:col-span-2 lg:col-span-1">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Identitas Penghuni</span>
                            <span class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </span>
                        </div>
                        <div class="text-lg font-bold text-slate-900 dark:text-white truncate">
                            {{ $tenant->name }}
                        </div>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            NIK: {{ substr($tenant->nik, 0, 6) }}******{{ substr($tenant->nik, -4) }}
                        </p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            HP: {{ $tenant->phone }}
                        </p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                        <a href="{{ route('portal.profile.edit') }}" class="text-emerald-600 dark:text-emerald-400 font-semibold hover:underline">
                            Edit Profil & Kontak &rarr;
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Roadmap / Menu Layanan Mandiri Penghuni -->
        <div class="mt-8">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-4">Layanan Portal Penghuni</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Kamar Saya -->
                <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm opacity-90">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Kamar Saya</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Detail fasilitas kamar dan aturan kost.</p>
                    <span class="inline-block mt-3 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded">
                        Rilis Fase F2.3
                    </span>
                </div>

                <!-- Tagihan & Pembayaran -->
                <a href="{{ route('portal.invoices.index') }}" class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-emerald-500 transition-all block">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Tagihan & Bayar</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Upload bukti bayar dan download kuitansi.</p>
                    <span class="inline-block mt-3 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded">
                        Rilis Fase F2.4
                    </span>
                </a>

                <!-- Lapor Kerusakan / Maintenance -->
                <a href="{{ route('portal.maintenance.index') }}" class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-emerald-500 transition-all block">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Lapor Perbaikan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Lapor kerusakan fasilitas kamar dan pantau progres.</p>
                    <span class="inline-block mt-3 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded">
                        Rilis Fase F2.5
                    </span>
                </a>

                <!-- Permohonan Izin -->
                <a href="{{ route('portal.permissions.index') }}" class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-emerald-500 transition-all block">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 dark:bg-teal-950/60 text-teal-600 dark:text-teal-400 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Permohonan Izin</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Izin tamu menginap, pulang larut, dan barang elektronik.</p>
                    <span class="inline-block mt-3 text-[10px] font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/60 px-2 py-0.5 rounded">
                        Rilis Fase F2.6
                    </span>
                </a>

                <!-- Keluar Kost (Move-Out) -->
                <a href="{{ route('portal.move-outs.index') }}" class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm hover:border-rose-500 transition-all block">
                    <div class="w-10 h-10 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Keluar Kost (Move-Out)</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pengajuan keluar kost, inspeksi kamar, dan settlement deposit.</p>
                    <span class="inline-block mt-3 text-[10px] font-semibold text-rose-700 dark:text-rose-400 bg-rose-50 dark:bg-rose-950/60 px-2 py-0.5 rounded">
                        Rilis Fase F2.7
                    </span>
                </a>

                <!-- Dokumen Kontrak -->
                <div class="p-5 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm opacity-90">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">Dokumen Sewa</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Arsip surat perjanjian sewa digital.</p>
                    <span class="inline-block mt-3 text-[10px] font-semibold text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-950/60 px-2 py-0.5 rounded">
                        Rilis Fase F2.8
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
