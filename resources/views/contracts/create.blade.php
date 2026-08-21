<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Kontrak Sewa Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                @if ($errors->has('error'))
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('contracts.store') }}" x-data="{
                    rentPrice: {{ old('rent_price', 0) }},
                    depositPrice: {{ old('deposit_amount', 0) }},
                    updatePrices(select) {
                        if(select.selectedIndex > 0) {
                            let option = select.options[select.selectedIndex];
                            this.rentPrice = option.dataset.rent;
                            this.depositPrice = option.dataset.deposit;
                        } else {
                            this.rentPrice = 0;
                            this.depositPrice = 0;
                        }
                    }
                }">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        <!-- Pihak Terlibat -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Pihak & Properti</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Penghuni <span class="text-red-500">*</span></label>
                                <x-ui.select name="tenant_id" required>
                                    <option value="" disabled selected>-- Pilih Penghuni --</option>
                                    @foreach ($tenants as $tenant)
                                        <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                            {{ $tenant->name }} (NIK: {{ substr($tenant->nik, 0, 6) }}...)
                                        </option>
                                    @endforeach
                                </x-ui.select>
                                @error('tenant_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Kamar (Tersedia) <span class="text-red-500">*</span></label>
                                <x-ui.select name="room_id" required @change="updatePrices($event.target)">
                                    <option value="" disabled selected>-- Pilih Kamar --</option>
                                    @foreach ($rooms as $room)
                                        <option value="{{ $room->id }}" 
                                                data-rent="{{ (int)$room->monthly_price }}" 
                                                data-deposit="{{ (int)$room->deposit_price }}"
                                                {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                            Kamar {{ $room->room_number }} - {{ $room->roomType?->name ?? '-' }}
                                        </option>
                                    @endforeach
                                </x-ui.select>
                                @if($rooms->isEmpty())
                                    <p class="text-yellow-600 text-xs mt-1">Tidak ada kamar berstatus 'Available'.</p>
                                @endif
                                @error('room_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Durasi & Harga -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Periode & Biaya</h3>
                            
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Mulai <span class="text-red-500">*</span></label>
                                    <x-ui.input type="date" name="start_date" value="{{ old('start_date') }}" required />
                                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Selesai <span class="text-red-500">*</span></label>
                                    <x-ui.input type="date" name="end_date" value="{{ old('end_date') }}" required />
                                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Sewa Deal (Rp) <span class="text-red-500">*</span></label>
                                <x-ui.input type="number" name="rent_price" x-model="rentPrice" required min="0" />
                                <p class="text-xs text-gray-500 mt-1">Otomatis terisi dari harga dasar kamar, bisa diubah.</p>
                                @error('rent_price') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deposit (Rp) <span class="text-red-500">*</span></label>
                                <x-ui.input type="number" name="deposit_amount" x-model="depositPrice" required min="0" />
                                @error('deposit_amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan Tambahan (Opsional)</label>
                        <x-ui.textarea name="notes" rows="3">{{ old('notes') }}</x-ui.textarea>
                        @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="mt-8 flex gap-3">
                        <x-ui.button type="submit">
                            Buat Kontrak
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('contracts.index') }}">
                            Batal
                        </x-ui.button>
                    </div>
                </form>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
