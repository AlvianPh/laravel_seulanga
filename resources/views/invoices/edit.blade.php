<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Komponen Tagihan: INV-') . $invoice->id }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900 border border-indigo-200 dark:border-indigo-700 rounded flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-indigo-800 dark:text-indigo-200">
                            Tagihan Bulan {{ $invoice->month }} Tahun {{ $invoice->year }}
                        </h3>
                        <p class="text-sm text-indigo-700 dark:text-indigo-300">
                            Penghuni: {{ $invoice->tenant->name ?? 'Dihapus' }} (Kamar {{ $invoice->room->room_number ?? '?' }})
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Jatuh Tempo</span>
                        <div class="font-bold text-red-600">{{ $invoice->due_date->format('d M Y') }}</div>
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
                        <div>
                            <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-4 border-b pb-2">Komponen Biaya (Rp)</h4>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">Sewa Kamar Dasar (Tetap)</label>
                                <input type="number" readonly value="{{ (int)$invoice->rent_amount }}" class="w-full border rounded px-3 py-2 bg-gray-100 text-gray-600 cursor-not-allowed">
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Listrik</label>
                                <input type="number" name="electricity_fee" x-model.number="electricity" min="0" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('electricity_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Air</label>
                                <input type="number" name="water_fee" x-model.number="water" min="0" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('water_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Internet / WiFi</label>
                                <input type="number" name="internet_fee" x-model.number="internet" min="0" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('internet_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Denda Keterlambatan / Kerusakan</label>
                                <input type="number" name="penalty_fee" x-model.number="penalty" min="0" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('penalty_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lain-lain</label>
                                <input type="number" name="other_fee" x-model.number="other" min="0" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('other_fee') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Panel Status & Live Preview -->
                        <div class="flex flex-col gap-6">
                            
                            <!-- Status Update -->
                            <div class="bg-gray-50 dark:bg-gray-700 border dark:border-gray-600 rounded p-4">
                                <h4 class="font-semibold text-gray-700 dark:text-gray-300 mb-3 border-b pb-1">Status Tagihan</h4>
                                
                                <select name="status" class="w-full border rounded px-3 py-2 dark:bg-gray-800 dark:border-gray-500 dark:text-white">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status->value }}" {{ $invoice->status->value === $status->value ? 'selected' : '' }}>
                                            {{ $status->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-2">Ubah manual jika ada penyesuaian khusus. Idealnya diubah jadi 'Paid' saat pembayaran diterima.</p>
                                @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Live Total -->
                            <div class="bg-indigo-600 text-white rounded p-6 shadow-lg text-center mt-auto">
                                <span class="block text-indigo-200 text-sm font-medium mb-1 uppercase tracking-wider">Estimasi Total Tagihan</span>
                                <div class="text-3xl font-bold font-mono" x-text="formatRupiah(total)">Rp 0</div>
                                <p class="text-xs text-indigo-300 mt-2">Total akan dihitung ulang secara akurat saat disave.</p>
                            </div>

                            <div class="flex gap-3">
                                <button type="submit" class="w-full bg-green-600 text-white font-semibold py-3 rounded hover:bg-green-700 shadow text-lg">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </div>
                        
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
