<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Kamar: ') . $room->room_number }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('rooms.edit', $room) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Edit Kamar</a>
                <a href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Kolom Kiri: Galeri Foto -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Galeri Foto</h3>
                            
                            @if($room->photos->isEmpty())
                                <div class="bg-gray-100 dark:bg-gray-700 rounded h-64 flex items-center justify-center text-gray-500">
                                    Belum ada foto untuk kamar ini.
                                </div>
                            @else
                                <!-- Foto Utama -->
                                @php
                                    $primaryPhoto = $room->photos->where('is_primary', true)->first() ?? $room->photos->first();
                                    $otherPhotos = $room->photos->where('id', '!=', $primaryPhoto->id);
                                @endphp
                                
                                <div class="mb-4">
                                    <img src="{{ Storage::url($primaryPhoto->file_path) }}" class="w-full h-96 object-cover rounded shadow" alt="Foto utama kamar">
                                </div>
                                
                                <!-- Thumbnail lainnya -->
                                @if($otherPhotos->isNotEmpty())
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach($otherPhotos as $photo)
                                            <img src="{{ Storage::url($photo->file_path) }}" class="w-full h-24 object-cover rounded opacity-80 hover:opacity-100 cursor-pointer" alt="Foto kamar">
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Detail & Fasilitas -->
                <div>
                    <!-- Detail Harga & Spesifikasi -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4 border-b pb-2">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Spesifikasi</h3>
                                <span class="px-2 py-1 rounded text-xs font-semibold
                                    @if($room->status->value === 'available') bg-green-100 text-green-700
                                    @elseif($room->status->value === 'occupied') bg-blue-100 text-blue-700
                                    @else bg-yellow-100 text-yellow-700 @endif
                                ">
                                    {{ $room->status->label() }}
                                </span>
                            </div>

                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Lantai</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $room->floor }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Tipe</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $room->type->label() }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500 dark:text-gray-400">Luas (m²)</dt>
                                    <dd class="font-medium text-gray-900 dark:text-gray-100">{{ $room->size_m2 }}</dd>
                                </div>
                            </dl>

                            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <dl class="space-y-3 text-sm">
                                    <div class="flex justify-between text-lg font-bold">
                                        <dt class="text-gray-900 dark:text-gray-100">Harga/Bulan</dt>
                                        <dd class="text-indigo-600 dark:text-indigo-400">Rp {{ number_format($room->monthly_price, 0, ',', '.') }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500 dark:text-gray-400">Deposit</dt>
                                        <dd class="font-medium text-gray-900 dark:text-gray-100">Rp {{ number_format($room->deposit_price, 0, ',', '.') }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <!-- Fasilitas -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Fasilitas</h3>
                            
                            @if(is_array($room->facilities) && count($room->facilities) > 0)
                                <ul class="grid grid-cols-2 gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    @foreach($room->facilities as $facility)
                                        <li class="flex items-center">
                                            <svg class="h-4 w-4 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $facility }}
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500 italic text-sm">Tidak ada fasilitas spesifik yang tercatat.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Riwayat Kontrak Singkat (opsional, disiapkan placeholder) -->
            <div class="mt-6 bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Kontrak Terakhir</h3>
                    @if($room->contracts->isEmpty())
                        <p class="text-gray-500 italic text-sm">Belum ada riwayat kontrak untuk kamar ini.</p>
                    @else
                        <table class="w-full text-sm text-left mt-2">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2">ID Penghuni</th>
                                    <th class="px-4 py-2">Mulai</th>
                                    <th class="px-4 py-2">Selesai</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($room->contracts as $contract)
                                    <tr class="border-b dark:border-gray-600">
                                        <td class="px-4 py-2">#{{ $contract->tenant_id }}</td>
                                        <td class="px-4 py-2">{{ $contract->start_date->format('d M Y') }}</td>
                                        <td class="px-4 py-2">{{ $contract->end_date->format('d M Y') }}</td>
                                        <td class="px-4 py-2">
                                            {{ $contract->status->label() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
