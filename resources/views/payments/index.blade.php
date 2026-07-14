<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Riwayat Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Transaksi Pembayaran</h3>
                        
                        <a href="{{ route('payments.create') }}" class="px-4 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700">
                            + Input Pembayaran
                        </a>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('payments.index') }}" class="mb-6 flex flex-col md:flex-row gap-4" x-data x-ref="form">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama penghuni atau no tagihan..."
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="w-full md:w-48">
                            <select name="method" @change="$refs.form.submit()" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Semua Metode</option>
                                @foreach ($methods as $method)
                                    <option value="{{ $method->value }}" {{ request('method') === $method->value ? 'selected' : '' }}>
                                        {{ $method->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full md:w-48">
                            <select name="status" @change="$refs.form.submit()" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                                Cari
                            </button>
                            @if(request()->anyFilled(['search', 'status', 'method']))
                                <a href="{{ route('payments.index') }}" class="ml-2 text-sm text-indigo-600 hover:underline">Reset</a>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                                <tr>
                                    <th class="px-4 py-3">ID Pembayaran</th>
                                    <th class="px-4 py-3">Tgl Bayar</th>
                                    <th class="px-4 py-3">Tagihan / Penghuni</th>
                                    <th class="px-4 py-3">Nominal</th>
                                    <th class="px-4 py-3">Metode</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($payments as $payment)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">PAY-{{ $payment->id }}</td>
                                        <td class="px-4 py-3 font-medium">{{ $payment->payment_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900 dark:text-gray-100">INV-{{ $payment->invoice_id }}</div>
                                            <div class="text-xs text-gray-500">{{ $payment->tenant->name ?? 'Dihapus' }}</div>
                                        </td>
                                        <td class="px-4 py-3 font-bold text-indigo-600">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">{{ $payment->method->label() }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded text-xs font-semibold
                                                @if($payment->status->value === 'verified') bg-green-100 text-green-700
                                                @elseif($payment->status->value === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($payment->status->value === 'rejected') bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-700 @endif
                                            ">
                                                {{ $payment->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <a href="{{ route('payments.show', $payment) }}" class="text-blue-600 hover:underline">Detail</a>
                                            
                                            <!-- Tombol Verifikasi KHUSUS Owner -->
                                            @can('verify', $payment)
                                                @if($payment->status->value === 'pending')
                                                    <a href="{{ route('payments.verify', $payment) }}" class="text-green-600 hover:underline font-semibold ml-2">Verifikasi</a>
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
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $payments->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
