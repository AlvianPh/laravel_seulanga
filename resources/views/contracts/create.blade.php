<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Kontrak Sewa Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Buat Kontrak Sewa Baru</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftarkan masa sewa kamar, nominal tarif kesepakatan, dan deposit jaminan penyewa</p>
            </div>

            @if ($errors->has('error'))
                <x-ui.alert type="error" class="mb-6">
                    {{ $errors->first('error') }}
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('contracts.store') }}" x-data="{
                rentPrice: {{ old('rent_price', 0) }},
                depositPrice: {{ old('deposit_amount', 0) }},
                startDate: '{{ old('start_date', date('Y-m-d')) }}',
                endDate: '{{ old('end_date', '') }}',
                updatePrices(select) {
                    if(select.selectedIndex > 0) {
                        let option = select.options[select.selectedIndex];
                        this.rentPrice = option.dataset.rent;
                        this.depositPrice = option.dataset.deposit;
                    } else {
                        this.rentPrice = 0;
                        this.depositPrice = 0;
                    }
                },
                setDuration(months) {
                    if (!this.startDate) return;
                    let d = new Date(this.startDate);
                    d.setMonth(d.getMonth() + months);
                    this.endDate = d.toISOString().split('T')[0];
                }
            }" x-init="if(!endDate && startDate) setDuration(1)">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Pihak Terlibat -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Pihak & Kamar</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Pilih Penghuni <span class="text-red-500">*</span></label>
                            <x-ui.select name="tenant_id" required class="text-sm">
                                <option value="" disabled selected>-- Pilih Penghuni --</option>
                                @foreach ($tenants as $tenant)
                                    <option value="{{ $tenant->id }}" {{ old('tenant_id') == $tenant->id ? 'selected' : '' }}>
                                        {{ $tenant->name }} (NIK: {{ substr($tenant->nik, 0, 6) }}...)
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('tenant_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Pilih Kamar (Tersedia) <span class="text-red-500">*</span></label>
                            <x-ui.select name="room_id" required @change="updatePrices($event.target)" class="text-sm">
                                <option value="" disabled selected>-- Pilih Kamar --</option>
                                @foreach ($rooms as $room)
                                    <option value="{{ $room->id }}" 
                                            data-rent="{{ (int)$room->monthly_price }}" 
                                            data-deposit="{{ (int)$room->deposit_price }}"
                                            {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        Kamar {{ $room->room_number }} - {{ $room->roomType?->name ?? '-' }} (Lt. {{ $room->floor }})
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @if($rooms->isEmpty())
                                <p class="text-amber-600 dark:text-amber-400 text-xs mt-1 font-medium">Tidak ada kamar berstatus 'Available'.</p>
                            @endif
                            @error('room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Durasi & Harga -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Periode & Biaya Sewa</h4>
                        
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Mulai Masuk <span class="text-red-500">*</span></label>
                                <x-ui.input type="date" name="start_date" x-model="startDate" @change="setDuration(1)" required class="text-sm" />
                                @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Selesai Sewa <span class="text-red-500">*</span></label>
                                <x-ui.input type="date" name="end_date" x-model="endDate" required class="text-sm" />
                                @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Quick Preset Duration Buttons -->
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 block mb-1.5">Preset Durasi Cepat:</span>
                            <div class="flex flex-wrap gap-1.5">
                                <button type="button" @click="setDuration(1)" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 text-gray-700 dark:text-gray-300 border border-gray-200/80 dark:border-gray-600 transition-colors">1 Bulan</button>
                                <button type="button" @click="setDuration(3)" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 text-gray-700 dark:text-gray-300 border border-gray-200/80 dark:border-gray-600 transition-colors">3 Bulan</button>
                                <button type="button" @click="setDuration(6)" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 text-gray-700 dark:text-gray-300 border border-gray-200/80 dark:border-gray-600 transition-colors">6 Bulan</button>
                                <button type="button" @click="setDuration(12)" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 hover:bg-indigo-50 hover:text-indigo-600 dark:hover:bg-indigo-900/40 text-gray-700 dark:text-gray-300 border border-gray-200/80 dark:border-gray-600 transition-colors">1 Tahun</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Harga Sewa Kesepakatan (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="rent_price" x-model="rentPrice" required min="0" step="1000" class="text-sm" />
                            @error('rent_price') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Uang Jaminan / Deposit (Rp) <span class="text-red-500">*</span></label>
                            <x-ui.input type="number" name="deposit_amount" x-model="depositPrice" required min="0" step="1000" class="text-sm" />
                            @error('deposit_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                </div>

                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700/70">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Catatan Tambahan (Opsional)</label>
                    <x-ui.textarea name="notes" rows="3" class="text-sm" placeholder="Catatan perjanjian khusus sewa...">{{ old('notes') }}</x-ui.textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Buat Kontrak Sewa
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('contracts.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
