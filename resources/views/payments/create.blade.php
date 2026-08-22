<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Input Pembayaran Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Input Pembayaran Tagihan</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Catat penerimaan pembayaran dari penghuni kost dan lampirkan bukti transfer</p>
            </div>

            <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data" x-data="{
                methodId: '{{ old('payment_method_id', '') }}',
                methodsData: @json($paymentMethods->pluck('name', 'id')),
                isPhotoRequired() {
                    if (!this.methodId || !this.methodsData[this.methodId]) return false;
                    let name = this.methodsData[this.methodId];
                    return name === 'Transfer Bank' || name === 'QRIS';
                }
            }">
                @csrf

                <!-- Pilih Tagihan (Hanya yg pending/overdue) -->
                <div class="mb-4">
                    <label for="invoice_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Pilih Tagihan / Invoice <span class="text-red-500">*</span></label>
                    <x-ui.select name="invoice_id" id="invoice_id" required class="text-sm">
                        <option value="">-- Pilih Tagihan Terbit --</option>
                        @foreach($invoices as $inv)
                            <option value="{{ $inv->id }}" {{ old('invoice_id', $selectedInvoiceId ?? null) == $inv->id ? 'selected' : '' }}>
                                #{{ $inv->id }} - {{ $inv->tenant->name ?? '?' }} (Kamar {{ $inv->room->room_number ?? '?' }}) - Rp{{ number_format($inv->total_amount, 0, ',', '.') }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    @error('invoice_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    @if($invoices->isEmpty())
                        <p class="text-amber-600 dark:text-amber-400 text-xs mt-2 italic">Semua tagihan sudah lunas atau belum ada tagihan terbit.</p>
                    @endif
                </div>

                <!-- Jumlah Bayar -->
                <div class="mb-4">
                    <label for="amount" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Jumlah Nominal Bayar (Rp) <span class="text-red-500">*</span></label>
                    <x-ui.input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1" step="1000" class="text-sm" placeholder="Contoh: 1500000" />
                    <p class="text-[11px] text-gray-400 mt-1">Isi sesuai nominal uang yang ditransfer/dibayar oleh penghuni.</p>
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tanggal Pembayaran -->
                <div class="mb-4">
                    <label for="payment_date" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Pembayaran <span class="text-red-500">*</span></label>
                    <x-ui.input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required class="text-sm" />
                    @error('payment_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Metode Pembayaran -->
                <div class="mb-4">
                    <label for="payment_method_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Metode Pembayaran <span class="text-red-500">*</span></label>
                    <x-ui.select name="payment_method_id" id="payment_method_id" x-model="methodId" required class="text-sm">
                        <option value="">-- Pilih Saluran Pembayaran --</option>
                        @foreach($paymentMethods as $method)
                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                        @endforeach
                    </x-ui.select>
                    @error('payment_method_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Upload Bukti (Required if Transfer/QRIS) -->
                <div x-data="{
                    previewUrl: null,
                    handleFileChange(event) {
                        const file = event.target.files[0];
                        if (file && file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = (e) => { this.previewUrl = e.target.result; };
                            reader.readAsDataURL(file);
                        } else {
                            this.previewUrl = null;
                        }
                    }
                }" class="mb-4 p-4 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                    <label for="proof_photo" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Bukti Pembayaran / Struk Transfer
                        <span x-show="isPhotoRequired()" class="text-rose-500">*</span>
                    </label>

                    <div x-show="previewUrl" x-cloak class="mb-3">
                        <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 block mb-1">Preview Bukti Transfer:</span>
                        <img :src="previewUrl" alt="Preview Bukti Transfer" class="h-32 w-auto max-w-[200px] object-cover rounded-xl border-2 border-indigo-500/80 shadow-xs">
                    </div>

                    <input type="file" name="proof_photo" id="proof_photo" @change="handleFileChange($event)" accept="image/*"
                           class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 cursor-pointer">
                    <p class="text-[11px] text-gray-400 mt-1.5" x-show="isPhotoRequired()">Wajib diunggah untuk metode Transfer/QRIS agar dapat diverifikasi oleh Owner.</p>
                    @error('proof_photo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Catatan -->
                <div class="mb-6">
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Catatan Pembayaran (Opsional)</label>
                    <x-ui.textarea name="notes" id="notes" rows="3" class="text-sm" placeholder="Contoh: Ditransfer via Rekening BCA an Budi...">{{ old('notes') }}</x-ui.textarea>
                    @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Pembayaran
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('payments.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>

        </x-ui.card>
    </div>
</x-app-layout>
