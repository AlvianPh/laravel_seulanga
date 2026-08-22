<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Kontrak: #') . $contract->id }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Kontrak Sewa: #{{ $contract->id }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui rincian tarif kesepakatan, durasi masa sewa, atau catatan perjanjian</p>
            </div>

            @if ($errors->has('error'))
                <x-ui.alert type="error" class="mb-6">
                    {{ $errors->first('error') }}
                </x-ui.alert>
            @endif

            <div class="mb-6 p-4 bg-amber-50/80 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-800/60 rounded-2xl">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="text-xs text-amber-800 dark:text-amber-200 leading-relaxed">
                        <strong class="font-bold">Perhatian:</strong> Mengubah kamar atau penghuni pada kontrak yang sudah berjalan dapat mempengaruhi riwayat tagihan & pembayaran. Sebaiknya hanya ubah harga deal, tanggal, atau catatan. Untuk memindahkan penghuni ke kamar lain, akhiri kontrak ini dan buat kontrak baru.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('contracts.update', $contract) }}">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Pihak Terlibat -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Pihak & Kamar</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Penghuni Terdaftar <span class="text-red-500">*</span></label>
                            <x-ui.select name="tenant_id" required class="text-sm">
                                @foreach ($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id', $contract->tenant_id) == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} (NIK: {{ substr($tenant->nik, 0, 6) }}...)
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('tenant_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Unit Kamar <span class="text-red-500">*</span></label>
                            <x-ui.select name="room_id" required class="text-sm">
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id', $contract->room_id) == $room->id ? 'selected' : '' }}>
                                        Kamar {{ $room->room_number }} (Status: {{ $room->status->label() }})
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Durasi & Harga -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Periode & Biaya Sewa</h4>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Mulai <span class="text-red-500">*</span></label>
                                <x-ui.input type="date" name="start_date" value="{{ old('start_date', $contract->start_date->format('Y-m-d')) }}" required class="text-sm" />
                                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Selesai <span class="text-red-500">*</span></label>
                                <x-ui.input type="date" name="end_date" value="{{ old('end_date', $contract->end_date->format('Y-m-d')) }}" required class="text-sm" />
                                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Harga Sewa Deal (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="rent_price" value="{{ old('rent_price', (int)$contract->rent_price) }}" required min="0" step="1000" class="text-sm" />
                            @error('rent_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Deposit (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="deposit_amount" value="{{ old('deposit_amount', (int)$contract->deposit_amount) }}" required min="0" step="1000" class="text-sm" />
                            @error('deposit_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                </div>

                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700/70">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Catatan Tambahan</label>
                    <x-ui.textarea name="notes" rows="3" class="text-sm">{{ old('notes', $contract->notes) }}</x-ui.textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Perubahan
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('contracts.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
