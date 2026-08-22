<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Rincian Pengeluaran') }}
            </h2>
            <x-ui.button variant="secondary" href="{{ route('expenses.index') }}">Kembali</x-ui.button>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Rincian Pengeluaran Operasional</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Detail informasi transaksi biaya, waktu pencatatan, dan lampiran nota</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="secondary" size="sm" href="{{ route('expenses.index') }}">Kembali</x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Panel Data -->
            <x-ui.card>
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Informasi Transaksi</h4>
                
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2 text-xs">
                    <div>
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Kategori Biaya</dt>
                        <dd class="mt-1">
                            <span class="px-2.5 py-1 bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg text-xs font-bold">{{ $expense->expenseCategory->name }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Tanggal Transaksi</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $expense->expense_date->format('d F Y') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-bold uppercase tracking-wider text-gray-400">Nominal Pengeluaran</dt>
                        <dd class="mt-1 font-mono font-black text-rose-600 dark:text-rose-400 text-2xl">Rp {{ number_format($expense->amount, 0, ',', '.') }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="font-bold uppercase tracking-wider text-gray-400 mb-1">Deskripsi / Keterangan</dt>
                        <dd class="text-xs text-gray-800 dark:text-gray-200 p-3 bg-gray-50/80 dark:bg-gray-800/60 rounded-xl border border-gray-100 dark:border-gray-700/60 leading-relaxed">
                            {{ $expense->description }}
                        </dd>
                    </div>
                    <div class="sm:col-span-2 border-t border-gray-100 dark:border-gray-700/60 pt-3 text-[11px] text-gray-400">
                        Dicatat Oleh: <strong class="text-gray-700 dark:text-gray-300">{{ $expense->creator->name ?? 'Sistem' }}</strong> 
                        @if($expense->creator)
                            ({{ $expense->creator->role->value }})
                        @endif
                        pada {{ $expense->created_at->format('d M Y H:i') }}
                    </div>
                </dl>

                <div class="mt-6 flex items-center gap-3 border-t border-gray-100 dark:border-gray-700/70 pt-6">
                    <x-ui.button variant="warning" size="sm" href="{{ route('expenses.edit', $expense) }}" class="flex-1">
                        Edit Data
                    </x-ui.button>
                    <x-ui.button variant="danger" size="sm" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('expenses.destroy', $expense) }}', name: 'data pengeluaran ini' })" class="flex-1">
                        Hapus Data
                    </x-ui.button>
                </div>

            </x-ui.card>

            <!-- Panel Bukti Foto -->
            <x-ui.card>
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Bukti Struk / Nota Pembelian</h4>
                
                @if($expense->receipt_path)
                    <div class="rounded-2xl border border-gray-200/80 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 p-2 flex justify-center overflow-hidden">
                        <img src="{{ asset('storage/' . $expense->receipt_path) }}" alt="Bukti Nota" class="max-w-full h-auto max-h-96 object-contain rounded-xl">
                    </div>
                    <div class="mt-4 text-center">
                        <a href="{{ asset('storage/' . $expense->receipt_path) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 dark:text-indigo-400 text-xs font-semibold hover:underline">
                            <span>Buka Gambar Resolusi Penuh</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                @else
                    <div class="h-64 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl flex flex-col items-center justify-center text-gray-400 p-6 text-center text-xs">
                        <svg class="w-10 h-10 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="font-bold text-gray-600 dark:text-gray-300">Tidak Ada Lampiran Struk</p>
                        <p class="text-gray-400 mt-1">Pengeluaran ini dicatat tanpa mengunggah file foto nota pembelian.</p>
                    </div>
                @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
