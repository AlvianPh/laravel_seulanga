<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Kontrak: #') . $contract->id }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('contracts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->has('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                    {{ $errors->first('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Detail Utama -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex justify-between items-center border-b pb-4 mb-4">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Informasi Kontrak</h3>
                            <span class="px-3 py-1 text-sm rounded font-bold
                                @if($contract->status->value === 'active') bg-green-100 text-green-700
                                @elseif($contract->status->value === 'ended') bg-gray-100 text-gray-700
                                @else bg-red-100 text-red-700 @endif
                            ">
                                {{ $contract->status->label() }}
                            </span>
                        </div>

                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Penghuni</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('tenants.show', $contract->tenant_id) }}" class="text-indigo-600 hover:underline">
                                        {{ $contract->tenant->name ?? 'Dihapus' }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Kamar</dt>
                                <dd class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('rooms.show', $contract->room_id) }}" class="text-indigo-600 hover:underline">
                                        {{ $contract->room->room_number ?? 'Dihapus' }}
                                    </a>
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Tanggal Mulai</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 font-medium">{{ $contract->start_date->format('d F Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Tanggal Selesai</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 font-medium">{{ $contract->end_date->format('d F Y') }}</dd>
                            </div>

                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Harga Sewa Bulanan (Deal)</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 text-lg text-green-600 font-bold">
                                    Rp {{ number_format($contract->rent_price, 0, ',', '.') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Deposit Dibayar</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 font-medium">
                                    Rp {{ number_format($contract->deposit_amount, 0, ',', '.') }}
                                </dd>
                            </div>

                            <div class="sm:col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Catatan Tambahan</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100 bg-gray-50 dark:bg-gray-700 p-3 rounded">
                                    {{ $contract->notes ?: 'Tidak ada catatan.' }}
                                </dd>
                            </div>
                            
                            <div class="sm:col-span-2 text-xs text-gray-400 mt-4">
                                Dibuat oleh: {{ $contract->creator->name ?? 'Sistem' }} pada {{ $contract->created_at->format('d M Y H:i') }}
                            </div>
                        </dl>
                        
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('contracts.edit', $contract) }}" class="text-indigo-600 hover:underline text-sm">Edit Data Dasar Kontrak</a>
                        </div>
                    </div>
                </div>

                <!-- Aksi Khusus -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Tindakan Kontrak</h3>
                        
                        @if($contract->isActive())
                            <div class="space-y-4">
                                <!-- Form Perpanjang -->
                                <div class="bg-indigo-50 dark:bg-indigo-900 p-4 rounded border border-indigo-100 dark:border-indigo-800" x-data="{ open: false }">
                                    <button @click="open = !open" class="w-full text-left font-semibold text-indigo-800 dark:text-indigo-200 flex justify-between items-center">
                                        Perpanjang Kontrak
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>
                                    
                                    <div x-show="open" class="mt-4 border-t border-indigo-200 pt-4" style="display: none;">
                                        <form method="POST" action="{{ route('contracts.renew', $contract) }}">
                                            @csrf
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tgl Mulai Lanjutan</label>
                                                <!-- Default start date adalah besoknya dari end_date kontrak lama -->
                                                <input type="date" name="start_date" value="{{ $contract->end_date->copy()->addDay()->format('Y-m-d') }}" required class="w-full text-sm border rounded px-2 py-1">
                                            </div>
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Tgl Selesai Baru</label>
                                                <input type="date" name="end_date" value="{{ $contract->end_date->copy()->addMonth()->format('Y-m-d') }}" required class="w-full text-sm border rounded px-2 py-1">
                                            </div>
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Sewa Baru</label>
                                                <input type="number" name="rent_price" value="{{ (int)$contract->rent_price }}" required class="w-full text-sm border rounded px-2 py-1">
                                            </div>
                                            <div class="mb-3">
                                                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Deposit</label>
                                                <input type="number" name="deposit_amount" value="{{ (int)$contract->deposit_amount }}" required class="w-full text-sm border rounded px-2 py-1">
                                            </div>
                                            <button type="submit" class="w-full bg-indigo-600 text-white text-sm py-2 rounded hover:bg-indigo-700">Submit Perpanjangan</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Form Akhiri Paksa -->
                                <div class="bg-red-50 dark:bg-red-900 p-4 rounded border border-red-100 dark:border-red-800">
                                    <p class="text-xs text-red-800 dark:text-red-200 mb-2">Mengakhiri kontrak akan membuat status kamar menjadi <strong>Available</strong>.</p>
                                    <form method="POST" action="{{ route('contracts.terminate', $contract) }}" onsubmit="return confirm('Yakin ingin mengakhiri kontrak ini sekarang? Status kamar akan dikembalikan jadi available.')">
                                        @csrf
                                        <button type="submit" class="w-full bg-red-600 text-white text-sm py-2 rounded hover:bg-red-700">Akhiri Kontrak</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <p class="text-gray-500 italic text-sm">Kontrak ini sudah selesai/diakhiri. Tidak ada tindakan yang dapat dilakukan.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
