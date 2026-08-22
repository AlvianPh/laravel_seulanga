<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Kamar: ') . $room->room_number }}
            </h2>
            <div class="space-x-2">
                <x-ui.button variant="warning" href="{{ route('rooms.edit', $room) }}">Edit Kamar</x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('rooms.index') }}">Kembali</x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Kamar: {{ $room->room_number }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Spesifikasi kamar, galeri foto, fasilitas dan riwayat sewa</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="primary" size="sm" href="{{ route('rooms.edit', $room) }}">Edit Kamar</x-ui.button>
                <x-ui.button variant="secondary" size="sm" href="{{ route('rooms.index') }}">Kembali</x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Galeri Foto -->
            <div class="lg:col-span-2 space-y-6">
                <x-ui.card>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Galeri Foto Kamar</h4>
                    
                    @if($room->photos->isEmpty())
                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl h-72 flex flex-col items-center justify-center text-gray-400 border border-dashed border-gray-200 dark:border-gray-700">
                            <svg class="w-12 h-12 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs">Belum ada foto yang diunggah untuk kamar ini.</span>
                        </div>
                    @else
                        @php
                            $primaryPhoto = $room->photos->where('is_primary', true)->first() ?? $room->photos->first();
                            $otherPhotos = $room->photos->where('id', '!=', $primaryPhoto->id);
                        @endphp
                        
                        <div class="mb-4 overflow-hidden rounded-2xl border border-gray-200/80 dark:border-gray-700">
                            <img src="{{ Storage::url($primaryPhoto->file_path) }}" class="w-full h-80 sm:h-96 object-cover" alt="Foto utama kamar">
                        </div>
                        
                        @if($otherPhotos->isNotEmpty())
                            <div class="grid grid-cols-4 gap-3">
                                @foreach($otherPhotos as $photo)
                                    <div class="overflow-hidden rounded-xl border border-gray-200/80 dark:border-gray-700">
                                        <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-20 sm:h-24 object-cover hover:scale-105 transition-transform cursor-pointer" alt="Foto kamar">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </x-ui.card>

                <!-- Riwayat Kontrak Singkat -->
                <x-ui.card>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Riwayat Kontrak Terakhir</h4>
                    @if($room->contracts->isEmpty())
                        <p class="text-gray-400 italic text-xs">Belum ada riwayat kontrak untuk kamar ini.</p>
                    @else
                        <x-ui.table-wrapper>
                            <x-slot name="header">
                                <tr>
                                    <th class="px-4 py-3">Penghuni</th>
                                    <th class="px-4 py-3">Mulai</th>
                                    <th class="px-4 py-3">Selesai</th>
                                    <th class="px-4 py-3">Status</th>
                                </tr>
                            </x-slot>
                            @foreach($room->contracts as $contract)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $contract->tenant->name ?? 'Penghuni #'.$contract->tenant_id }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $contract->start_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $contract->end_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <x-ui.badge :status="$contract->status">{{ $contract->status->label() }}</x-ui.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </x-ui.table-wrapper>
                    @endif
                </x-ui.card>
            </div>

            <!-- Kolom Kanan: Detail & Fasilitas -->
            <div class="space-y-6">
                <!-- Detail Harga & Spesifikasi -->
                <x-ui.card>
                    <div class="flex justify-between items-center mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100">Spesifikasi Kamar</h4>
                        <x-ui.badge :status="$room->status">{{ $room->status->label() }}</x-ui.badge>
                    </div>

                    <dl class="space-y-3 text-xs">
                        <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800">
                            <dt class="text-gray-500 dark:text-gray-400">Posisi Lantai</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">Lantai {{ $room->floor }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800">
                            <dt class="text-gray-500 dark:text-gray-400">Tipe Kamar</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">{{ $room->roomType?->name ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50 dark:border-gray-800">
                            <dt class="text-gray-500 dark:text-gray-400">Luas Kamar</dt>
                            <dd class="font-bold text-gray-900 dark:text-gray-100">{{ $room->size_m2 }} m²</dd>
                        </div>
                    </dl>

                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700/70 space-y-2">
                        <div class="flex justify-between items-baseline">
                            <span class="text-xs text-gray-500 dark:text-gray-400">Harga Sewa / Bln</span>
                            <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($room->monthly_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-baseline text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Deposit Jaminan</span>
                            <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($room->deposit_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Fasilitas -->
                <x-ui.card>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Fasilitas Tersedia</h4>
                    
                    @if($room->facilities->isNotEmpty())
                        <ul class="grid grid-cols-1 gap-2 text-xs text-gray-700 dark:text-gray-300">
                            @foreach($room->facilities as $facility)
                                <li class="flex items-center gap-2 p-2 rounded-xl bg-gray-50/80 dark:bg-gray-900/40">
                                    <svg class="h-4 w-4 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="font-medium">{{ $facility->name }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-400 italic text-xs">Tidak ada fasilitas khusus yang terdaftar.</p>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
