<x-portal-layout>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Katalog Kamar Tersedia</h1>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                    Pilih kamar idaman Anda dan ajukan sewa secara online dengan mudah.
                </p>
            </div>
            <a href="{{ route('portal.applications.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 border border-slate-200 dark:border-slate-800 shadow-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                Riwayat Pengajuan Saya
            </a>
        </div>

        <!-- Filter Bar -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <form method="GET" action="{{ route('portal.rooms.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Tipe Kamar</label>
                    <select name="room_type_id" class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Tipe Kamar</option>
                        @foreach ($roomTypes as $type)
                            <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Lantai</label>
                    <select name="floor" class="w-full text-sm rounded-xl border-slate-300 dark:border-slate-700 dark:bg-slate-800 text-slate-900 dark:text-white focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Semua Lantai</option>
                        <option value="1" {{ request('floor') == '1' ? 'selected' : '' }}>Lantai 1</option>
                        <option value="2" {{ request('floor') == '2' ? 'selected' : '' }}>Lantai 2</option>
                        <option value="3" {{ request('floor') == '3' ? 'selected' : '' }}>Lantai 3</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                        Terapkan Filter
                    </button>
                    @if (request()->hasAny(['room_type_id', 'floor']))
                        <a href="{{ route('portal.rooms.index') }}" class="py-2.5 px-3 rounded-xl text-sm text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Room Cards Grid -->
        @if ($rooms->isEmpty())
            <div class="p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-400 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Tidak Ada Kamar yang Tersedia</h3>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                    Saat ini semua kamar sesuai filter sedang terisi atau dalam pemeliharaan. Silakan cek kembali secara berkala.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($rooms as $room)
                    <div class="rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            <!-- Room Image -->
                            <div class="relative h-48 bg-slate-100 dark:bg-slate-800 overflow-hidden">
                                @php
                                    $primaryPhoto = $room->photos->firstWhere('is_primary', true) ?? $room->photos->first();
                                @endphp
                                @if ($primaryPhoto)
                                    <img src="{{ Storage::url($primaryPhoto->file_path) }}"
                                         alt="Kamar {{ $room->room_number }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center text-slate-400 dark:text-slate-600 bg-slate-100 dark:bg-slate-800">
                                        <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span class="text-xs">Foto belum tersedia</span>
                                    </div>
                                @endif
                                <span class="absolute top-3 right-3 px-2.5 py-1 text-xs font-bold rounded-full bg-emerald-500 text-white shadow-sm">
                                    Tersedia
                                </span>
                                <span class="absolute bottom-3 left-3 px-2.5 py-1 text-xs font-semibold rounded-lg bg-black/60 backdrop-blur-md text-white">
                                    Lantai {{ $room->floor }}
                                </span>
                            </div>

                            <!-- Content -->
                            <div class="p-5">
                                <div class="flex items-center justify-between mb-1.5">
                                    <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                                        Kamar {{ $room->room_number }}
                                    </h3>
                                    <span class="text-xs font-medium px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">
                                        {{ $room->roomType?->name ?? 'Standard' }}
                                    </span>
                                </div>

                                <div class="text-lg font-extrabold text-emerald-600 dark:text-emerald-400 mb-3">
                                    Rp {{ number_format($room->monthly_price, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-500 dark:text-slate-400">/bulan</span>
                                </div>

                                @if ($room->size_m2)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3 flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                                        </svg>
                                        Luas: {{ $room->size_m2 }} m²
                                    </p>
                                @endif

                                <!-- Facilities Preview -->
                                @if ($room->facilities->isNotEmpty())
                                    <div class="flex flex-wrap gap-1.5 mb-2">
                                        @foreach ($room->facilities->take(3) as $fac)
                                            <span class="text-[11px] font-medium px-2 py-0.5 rounded bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200/60 dark:border-slate-700/60">
                                                {{ $fac->name }}
                                            </span>
                                        @endforeach
                                        @if ($room->facilities->count() > 3)
                                            <span class="text-[11px] font-medium px-1.5 py-0.5 text-slate-400">
                                                +{{ $room->facilities->count() - 3 }} lainnya
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Footer CTA -->
                        <div class="p-5 pt-0">
                            <a href="{{ route('portal.rooms.show', $room) }}"
                               class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl text-sm font-semibold bg-slate-900 hover:bg-slate-800 text-white dark:bg-emerald-600 dark:hover:bg-emerald-700 shadow-sm transition-colors">
                                Lihat Detail & Ajukan
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $rooms->links() }}
            </div>
        @endif
    </div>
</x-portal-layout>
