<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Komponen Tagihan: INV-') . $invoice->id }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>

            <div class="mb-6 p-4 bg-indigo-50/80 dark:bg-indigo-950/40 border border-indigo-100 dark:border-indigo-900/60 rounded-2xl flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div>
                    <h3 class="font-bold text-gray-900 dark:text-white">
                        Tagihan Bulan {{ $invoice->month }} Tahun {{ $invoice->year }} (INV-{{ $invoice->id }})
                    </h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        Penghuni: <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $invoice->tenant->name ?? 'Dihapus' }}</span> (Kamar {{ $invoice->room->room_number ?? '?' }})
                    </p>
                </div>
                <div class="sm:text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block">Jatuh Tempo</span>
                    <div class="text-sm font-bold text-rose-600 dark:text-rose-400">{{ $invoice->due_date->format('d M Y') }}</div>
                </div>
            </div>

            <form method="POST" action="{{ route('invoices.update', $invoice) }}" x-data="{
                rent: {{ (float)$invoice->rent_amount }},
                electricity: {{ (float)$invoice->electricity_fee ?? 0 }},
                water: {{ (float)$invoice->water_fee ?? 0 }},
                internet: {{ (float)$invoice->internet_fee ?? 0 }},
                penalty: {{ (float)$invoice->penalty_fee ?? 0 }},
                other: {{ (float)$invoice->other_fee ?? 0 }},
                
                get total() {
                    return this.rent + 
                           (parseFloat(this.electricity) || 0) + 
                           (parseFloat(this.water) || 0) + 
                           (parseFloat(this.internet) || 0) + 
                           (parseFloat(this.penalty) || 0) + 
                           (parseFloat(this.other) || 0);
                },
                
                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
                }
            }">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    
                    <!-- Form Edit Fee -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Komponen Biaya (Rp)</h4>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1.5">Sewa Kamar Dasar (Tetap)</label>
                            <input type="number" readonly value="{{ (int)$invoice->rent_amount }}" class="w-full text-sm border border-gray-200 dark:border-gray-700 rounded-xl px-3 py-2 bg-gray-50 dark:bg-gray-800 text-gray-500 cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Biaya Listrik</label>
                            <x-ui.input type="number" name="electricity_fee" x-model.number="electricity" min="0" step="1000" class="text-sm" />
                            @error('electricity_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Biaya Air</label>
                            <x-ui.input type="number" name="water_fee" x-model.number="water" min="0" step="1000" class="text-sm" />
                            @error('water_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Internet / WiFi</label>
                            <x-ui.input type="number" name="internet_fee" x-model.number="internet" min="0" step="1000" class="text-sm" />
                            @error('internet_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Denda Keterlambatan / Kerusakan</label>
                            <x-ui.input type="number" name="penalty_fee" x-model.number="penalty" min="0" step="1000" class="text-sm" />
                            @error('penalty_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Biaya Lain-lain</label>
                            <x-ui.input type="number" name="other_fee" x-model.number="other" min="0" step="1000" class="text-sm" />
                            @error('other_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Panel Status & Live Preview -->
                    <div class="flex flex-col gap-6">
                        
                        <!-- Status Update -->
                        <div class="bg-gray-50/80 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-700/70 rounded-2xl p-5 space-y-2">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 border-b border-gray-100 dark:border-gray-700/60 pb-2">Status Pembayaran Tagihan</h4>
                            
                            <x-ui.select name="status" class="text-sm">
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ $invoice->status->value === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                            <p class="text-[11px] text-gray-400">Gunakan opsi ini jika Anda ingin memperbarui status invoice secara manual.</p>
                            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <!-- Live Total -->
                        <div class="bg-indigo-600 dark:bg-indigo-700 text-white rounded-2xl p-6 shadow-sm text-center mt-auto">
                            <span class="block text-indigo-200 text-xs font-bold uppercase tracking-wider mb-1">Estimasi Total Tagihan</span>
                            <div class="text-3xl font-black font-mono tracking-tight" x-text="formatRupiah(total)">Rp 0</div>
                            <p class="text-[11px] text-indigo-200/80 mt-2">Total akan tersimpan dan tercatat di rincian invoice.</p>
                        </div>

                        <div class="flex items-center gap-3">
                            <x-ui.button type="submit" variant="primary" class="w-full">
                                Simpan Perubahan
                            </x-ui.button>
                            <x-ui.button variant="secondary" href="{{ route('invoices.show', $invoice) }}" class="w-full">
                                Batal
                            </x-ui.button>
                        </div>
                    </div>
                    
                </div>
            </form>

        </x-ui.card>
    </div>
</x-app-layout>
