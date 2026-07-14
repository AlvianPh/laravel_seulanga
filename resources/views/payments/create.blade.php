<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Input Pembayaran Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

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
                        <label for="invoice_id" class="block font-medium text-gray-700 dark:text-gray-300">Pilih Tagihan</label>
                        <select name="invoice_id" id="invoice_id" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="">-- Pilih Tagihan --</option>
                            @foreach($invoices as $inv)
                                <option value="{{ $inv->id }}" {{ old('invoice_id', $selectedInvoiceId ?? null) == $inv->id ? 'selected' : '' }}>
                                    #{{ $inv->id }} - {{ $inv->tenant->name ?? '?' }} ({{ $inv->room->room_number ?? '?' }}) - Rp{{ number_format($inv->total_amount, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('invoice_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        @if($invoices->isEmpty())
                            <p class="text-yellow-600 text-xs mt-2 italic">Semua tagihan sudah lunas atau belum ada tagihan dibuat.</p>
                        @endif
                    </div>

                    <!-- Jumlah Bayar -->
                    <div class="mb-4">
                        <label for="amount" class="block font-medium text-gray-700 dark:text-gray-300">Jumlah Bayar (Rp)</label>
                        <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="1"
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="text-xs text-gray-500 mt-1">Isi sesuai nominal yang ditransfer/dibayar. Jika ada denda, sertakan juga.</p>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Tanggal Pembayaran -->
                    <div class="mb-4">
                        <label for="payment_date" class="block font-medium text-gray-700 dark:text-gray-300">Tanggal Pembayaran</label>
                        <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        @error('payment_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="mb-4">
                        <label for="payment_method_id" class="block font-medium text-gray-700 dark:text-gray-300">Metode Pembayaran</label>
                        <select name="payment_method_id" id="payment_method_id" x-model="methodId" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            <option value="">-- Pilih Metode --</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method->id }}">{{ $method->name }}</option>
                            @endforeach
                        </select>
                        @error('payment_method_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Upload Bukti (Required if Transfer/QRIS) -->
                    <div class="mb-4">
                        <label for="proof_photo" class="block font-medium text-gray-700 dark:text-gray-300">
                            Bukti Pembayaran / Struk
                            <span x-show="isPhotoRequired()" class="text-red-500">*</span>
                        </label>
                        <input type="file" name="proof_photo" id="proof_photo" accept="image/*"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:text-gray-300">
                        <p class="text-xs text-gray-500 mt-1" x-show="isPhotoRequired()">Wajib diunggah untuk metode Transfer/QRIS agar bisa diverifikasi Owner.</p>
                        @error('proof_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Catatan -->
                    <div class="mb-6">
                        <label for="notes" class="block font-medium text-gray-700 dark:text-gray-300">Catatan (Opsional)</label>
                        <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700">
                            Simpan Pembayaran
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
