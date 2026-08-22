<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Pembayaran') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <x-ui.card>

                @if (session('success'))
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Riwayat Transaksi Pembayaran</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar penerimaan uang sewa, bukti transfer, dan status verifikasi</p>
                    </div>
                    
                    <x-ui.button href="{{ route('payments.create') }}" variant="success" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Catat Pembayaran
                    </x-ui.button>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('payments.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3 items-center" x-data x-ref="form">
                    <div class="flex-1 w-full">
                        <x-ui.input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama penghuni atau no tagihan..." class="text-sm" />
                    </div>
                    <div class="w-full sm:w-48">
                        <x-ui.select name="payment_method_id" @change="$refs.form.submit()" class="text-sm">
                            <option value="">Semua Metode</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="w-full sm:w-44">
                        <x-ui.select name="status" @change="$refs.form.submit()" class="text-sm">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <x-ui.button type="submit" variant="primary" size="sm" class="flex-1 sm:flex-none">
                            Cari
                        </x-ui.button>
                        @if(request()->anyFilled(['search', 'status', 'payment_method_id']))
                            <x-ui.button href="{{ route('payments.index') }}" variant="secondary" size="sm">Reset</x-ui.button>
                        @endif
                    </div>
                </form>

                <!-- Table -->
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">ID Ref</th>
                            <th class="px-4 py-3.5">Tgl Bayar</th>
                            <th class="px-4 py-3.5">Tagihan & Penghuni</th>
                            <th class="px-4 py-3.5">Nominal Bayar</th>
                            <th class="px-4 py-3.5">Metode</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                            @forelse ($payments as $payment)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3.5 font-mono text-xs text-gray-500 dark:text-gray-400">#PAY-{{ $payment->id }}</td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300 font-medium">{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-gray-900 dark:text-white">INV-{{ $payment->invoice_id }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $payment->tenant->name ?? 'Dihapus' }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $payment->paymentMethod->name }}</td>
                                    <td class="px-4 py-3.5">
                                        <x-ui.badge :status="$payment->status">{{ $payment->status->label() }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('payments.show', $payment) }}">Detail</x-ui.button>
                                        
                                        <!-- Tombol Verifikasi KHUSUS Owner -->
                                        @can('verify', $payment)
                                            @if($payment->status->value === 'pending')
                                                <x-ui.button size="sm" variant="success" href="{{ route('payments.verify', $payment) }}">Verifikasi</x-ui.button>
                                            @endif
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <x-ui.empty-state colspan="7" icon="payment" title="Belum Ada Pembayaran" description="Catat transaksi pembayaran sewa yang diterima dari penghuni kost." action-text="Catat Pembayaran" action-url="{{ route('payments.create') }}" />
                            @endforelse
                </x-ui.table-wrapper>

                <div class="mt-6">
                    {{ $payments->links() }}
                </div>

        </x-ui.card>
    </div>
</x-app-layout>
