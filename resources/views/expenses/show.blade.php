<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Rincian Pengeluaran') }}
            </h2>
            <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Kembali</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Panel Data -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Informasi Transaksi</h3>
                    
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 text-sm">
                        <div class="sm:col-span-1">
                            <dt class="font-medium text-gray-500">Kategori</dt>
                            <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">
                                <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-bold">{{ $expense->category->label() }}</span>
                            </dd>
                        </div>
                        <div class="sm:col-span-1">
                            <dt class="font-medium text-gray-500">Tanggal Pengeluaran</dt>
                            <dd class="mt-1 font-semibold text-gray-900 dark:text-gray-100">{{ $expense->expense_date->format('d F Y') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-500">Nominal</dt>
                            <dd class="mt-1 font-bold text-red-600 text-2xl">Rp {{ number_format($expense->amount, 0, ',', '.') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="font-medium text-gray-500">Deskripsi/Keterangan</dt>
                            <dd class="mt-1 text-gray-800 dark:text-gray-200 p-3 bg-gray-50 dark:bg-gray-700 rounded border dark:border-gray-600">
                                {{ $expense->description }}
                            </dd>
                        </div>
                        <div class="sm:col-span-2 border-t pt-4">
                            <dt class="font-medium text-gray-500">Dicatat Oleh</dt>
                            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                                <strong>{{ $expense->creator->name ?? 'Sistem / Dihapus' }}</strong> 
                                @if($expense->creator)
                                    <span class="text-xs text-gray-500">({{ $expense->creator->role->value }})</span>
                                @endif
                                <br><span class="text-xs text-gray-400">Pada: {{ $expense->created_at->format('d/m/Y H:i') }}</span>
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-8 flex gap-3 border-t pt-6">
                        <a href="{{ route('expenses.edit', $expense) }}" class="flex-1 text-center bg-indigo-600 text-white font-bold py-2 rounded shadow hover:bg-indigo-700">
                            Edit Data
                        </a>
                        <form action="{{ route('expenses.destroy', $expense) }}" method="POST" class="flex-1" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengeluaran ini secara permanen?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-full bg-red-100 text-red-700 font-bold py-2 rounded border border-red-200 hover:bg-red-200">
                                Hapus Data
                            </button>
                        </form>
                    </div>

                </div>

                <!-- Panel Bukti Foto -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Bukti Lampiran (Struk/Nota)</h3>
                    
                    @if($expense->receipt_path)
                        <div class="border rounded bg-gray-50 dark:bg-gray-900 p-2 flex justify-center">
                            <img src="{{ asset('storage/' . $expense->receipt_path) }}" alt="Bukti Nota" class="max-w-full h-auto max-h-96 object-contain rounded">
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="text-indigo-600 font-medium hover:underline flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                Buka Gambar Resolusi Penuh
                            </a>
                        </div>
                    @else
                        <div class="h-64 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg flex flex-col items-center justify-center text-gray-400 p-6 text-center">
                            <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <p class="font-medium text-gray-500">Tidak Ada Lampiran</p>
                            <p class="text-sm mt-1">Pengeluaran ini dicatat tanpa mengunggah foto struk/nota pembelian.</p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
