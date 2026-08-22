<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Detail Kontrak: #') . $contract->id }}
            </h2>
            <div class="space-x-2">
                <x-ui.button variant="secondary" href="{{ route('contracts.index') }}">Kembali</x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        
        @if (session('success'))
            <x-ui.alert type="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif
        @if ($errors->has('error'))
            <x-ui.alert type="error">
                {{ $errors->first('error') }}
            </x-ui.alert>
        @endif

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Kontrak Sewa: #{{ $contract->id }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Informasi masa sewa kamar, data penghuni, nilai transaksi kesepakatan dan aksi perpanjangan</p>
            </div>
            <div class="flex items-center gap-2">
                @if($contract->isActive())
                    <x-ui.button variant="primary" size="sm" href="{{ route('contracts.edit', $contract) }}">Edit Kontrak</x-ui.button>
                @endif
                <x-ui.button variant="secondary" size="sm" href="{{ route('contracts.index') }}">Kembali</x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Detail Utama -->
            <div class="lg:col-span-2 space-y-6">
                <x-ui.card>
                    <div class="flex justify-between items-center border-b border-gray-100 dark:border-gray-700/70 pb-4 mb-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100">Rincian Informasi Kontrak</h4>
                        <x-ui.badge :status="$contract->status">{{ $contract->status->label() }}</x-ui.badge>
                    </div>

                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-5 text-xs">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Penghuni Penyewa</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">
                                <a href="{{ route('tenants.show', $contract->tenant_id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $contract->tenant->name ?? 'Dihapus' }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Unit Kamar</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">
                                <a href="{{ route('rooms.show', $contract->room_id) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    Kamar {{ $contract->room->room_number ?? 'Dihapus' }} (Lt. {{ $contract->room->floor ?? '-' }})
                                </a>
                            </dd>
                        </div>
                        
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Tanggal Mulai Sewa</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $contract->start_date->format('d F Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Tanggal Selesai Sewa</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $contract->end_date->format('d F Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Harga Sewa Bulanan (Deal)</dt>
                            <dd class="mt-1 text-base text-emerald-600 dark:text-emerald-400 font-bold">
                                Rp {{ number_format($contract->rent_price, 0, ',', '.') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Uang Jaminan / Deposit</dt>
                            <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-gray-100">
                                Rp {{ number_format($contract->deposit_amount, 0, ',', '.') }}
                            </dd>
                        </div>

                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Catatan Perjanjian</dt>
                            <dd class="mt-1 text-xs text-gray-800 dark:text-gray-200 bg-gray-50/80 dark:bg-gray-800/60 p-3 rounded-xl border border-gray-100 dark:border-gray-700/60">
                                {{ $contract->notes ?: 'Tidak ada catatan perjanjian khusus.' }}
                            </dd>
                        </div>
                        
                        <div class="sm:col-span-2 text-[11px] text-gray-400 pt-2 border-t border-gray-100 dark:border-gray-700/60">
                            Dibuat oleh: {{ $contract->creator->name ?? 'Sistem' }} pada {{ $contract->created_at->format('d M Y H:i') }}
                        </div>
                    </dl>
                </x-ui.card>
            </div>

            <!-- Aksi Khusus -->
            <div class="space-y-6">
                <x-ui.card>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Tindakan Kontrak</h4>
                    
                    @if($contract->isActive())
                        <div class="space-y-4">
                            <!-- Form Perpanjang -->
                            <div class="bg-indigo-50/70 dark:bg-indigo-950/40 p-4 rounded-2xl border border-indigo-100 dark:border-indigo-900/60" x-data="{ open: false }">
                                <button @click="open = !open" class="w-full text-left text-xs font-bold uppercase tracking-wider text-indigo-800 dark:text-indigo-200 flex justify-between items-center cursor-pointer">
                                    <span>Perpanjang Masa Sewa</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>
                                
                                <div x-show="open" x-cloak class="mt-4 border-t border-indigo-200/60 dark:border-indigo-800/60 pt-4">
                                    <form method="POST" action="{{ route('contracts.renew', $contract) }}" class="space-y-3">
                                        @csrf
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Tgl Mulai Lanjutan</label>
                                            <x-ui.input type="date" name="start_date" value="{{ $contract->end_date->copy()->addDay()->format('Y-m-d') }}" required class="text-xs" />
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Tgl Selesai Baru</label>
                                            <x-ui.input type="date" name="end_date" value="{{ $contract->end_date->copy()->addMonth()->format('Y-m-d') }}" required class="text-xs" />
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Harga Sewa Baru (Rp)</label>
                                            <x-ui.input type="number" name="rent_price" value="{{ (int)$contract->rent_price }}" required class="text-xs" />
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">Deposit (Rp)</label>
                                            <x-ui.input type="number" name="deposit_amount" value="{{ (int)$contract->deposit_amount }}" required class="text-xs" />
                                        </div>
                                        <x-ui.button variant="primary" type="submit" class="w-full text-xs mt-2">Submit Perpanjangan</x-ui.button>
                                    </form>
                                </div>
                            </div>

                            <!-- Form Akhiri Paksa -->
                            <div class="bg-rose-50/70 dark:bg-rose-950/40 p-4 rounded-2xl border border-rose-100 dark:border-rose-900/60">
                                <p class="text-xs text-rose-800 dark:text-rose-200 mb-3 leading-relaxed">Mengakhiri kontrak akan membuat status kamar otomatis kembali menjadi <strong class="font-bold">Available (Tersedia)</strong>.</p>
                                <form method="POST" action="{{ route('contracts.terminate', $contract) }}" onsubmit="return confirm('Yakin ingin mengakhiri kontrak ini sekarang? Status kamar akan dikembalikan jadi available.')">
                                    @csrf
                                    <x-ui.button variant="danger" type="submit" class="w-full text-xs">Akhiri Kontrak Sekarang</x-ui.button>
                                </form>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400 italic text-xs py-2">Kontrak ini sudah selesai/diakhiri. Tidak ada tindakan aktif yang dapat dilakukan.</p>
                    @endif
                </x-ui.card>
            </div>

        </div>
    </div>
</x-app-layout>
