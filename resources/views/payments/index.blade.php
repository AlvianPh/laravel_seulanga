<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                    @if (session('success'))
                        <x-ui.alert type="success">
                            {{ session('success') }}
                        </x-ui.alert>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Transaksi Pembayaran</h3>
                        
                        <x-ui.button href="{{ route('payments.create') }}">
                            + Input Pembayaran
                        </x-ui.button>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('payments.index') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-center" x-data x-ref="form">
                        <div class="flex-1 w-full">
                            <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama penghuni atau no tagihan..." />
                        </div>
                        <div class="w-full md:w-48">
                            <x-ui.select name="payment_method_id" @change="$refs.form.submit()">
                                <option value="">Semua Metode</option>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                        {{ $method->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>
                        <div class="w-full md:w-48">
                            <x-ui.select name="status" @change="$refs.form.submit()">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>
                        <div class="flex gap-2">
                            <x-ui.button type="submit" variant="secondary">
                                Cari
                            </x-ui.button>
                            @if(request()->anyFilled(['search', 'status', 'method']))
                                <x-ui.button href="{{ route('payments.index') }}" variant="secondary">Reset</x-ui.button>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <x-ui.table-wrapper>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3">ID Pembayaran</th>
                                <th class="px-4 py-3">Tgl Bayar</th>
                                <th class="px-4 py-3">Tagihan / Penghuni</th>
                                <th class="px-4 py-3">Nominal</th>
                                <th class="px-4 py-3">Metode</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </x-slot>
                                @forelse ($payments as $payment)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">PAY-{{ $payment->id }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900 dark:text-gray-100">INV-{{ $payment->invoice_id }}</div>
                                            <div class="text-xs text-gray-500">{{ $payment->tenant->name ?? 'Dihapus' }}</div>
                                        </td>
                                        <td class="px-4 py-3 font-bold text-indigo-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">{{ $payment->paymentMethod->name }}</td>
                                        <td class="px-4 py-3">
                                            <x-ui.badge :status="$payment->status">{{ $payment->status->label() }}</x-ui.badge>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
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
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Belum ada data pembayaran.
                                        </td>
                                    </tr>
                                @endforelse
                    </x-ui.table-wrapper>

                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
