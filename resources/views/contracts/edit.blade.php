<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kontrak: #') . $contract->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if ($errors->has('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">
                        <strong>Perhatian:</strong> Mengubah kamar atau penghuni pada kontrak yang sudah berjalan dapat merusak riwayat. Sebaiknya hanya ubah harga deal, tanggal, atau catatan. Untuk memindahkan penghuni ke kamar lain, akhiri kontrak ini dan buat kontrak baru.
                    </p>
                </div>

                <form method="POST" action="{{ route('contracts.update', $contract) }}">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Pihak Terlibat -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Pihak & Properti</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Penghuni <span class="text-red-500">*</span></label>
                                <select name="tenant_id" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id', $contract->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tenant_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kamar <span class="text-red-500">*</span></label>
                                <select name="room_id" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" {{ old('room_id', $contract->room_id) == $room->id ? 'selected' : '' }}>
                                            Kamar {{ $room->room_number }} (Status saat ini: {{ $room->status->label() }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('room_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Durasi & Harga -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Periode & Biaya</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mulai <span class="text-red-500">*</span></label>
                                    <input type="date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required
                                           class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selesai <span class="text-red-500">*</span></label>
                                    <input type="date" name="end_date" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" required
                                           class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Sewa Deal (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="rent_price" value="{{ old('rent_price', (int)$contract->rent_price) }}" required min="0"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('rent_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deposit (Rp) <span class="text-red-500">*</span></label>
                                <input type="number" name="deposit_amount" value="{{ old('deposit_amount', (int)$contract->deposit_amount) }}" required min="0"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('deposit_amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan</label>
                        <textarea name="notes" rows="3"
                                  class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes', $contract->notes) }}</textarea>
                        @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('contracts.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded hover:bg-gray-300">
                            Batal
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
