<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Tagihan') }}
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
                    @if (session('info'))
                        <div class="mb-4 p-4 bg-blue-100 text-blue-700 rounded">
                            {{ session('info') }}
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Tagihan Bulanan</h3>
                        
                        <!-- Manual Generate Button -->
                        <form method="POST" action="{{ route('invoices.generate-manual') }}">
                            @csrf
                            <div class="flex items-center space-x-2">
                                <select name="month" class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600">
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                                    @endfor
                                </select>
                                <select name="year" class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:border-gray-600">
                                    <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                    <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                                </select>
                                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded hover:bg-indigo-700">
                                    + Generate Manual
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('invoices.index') }}" class="mb-6 flex flex-col md:flex-row gap-4" x-data x-ref="form">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama penghuni..."
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="w-full md:w-32">
                            <select name="month" @change="$refs.form.submit()" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Semua Bln</option>
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="w-full md:w-32">
                            <select name="year" @change="$refs.form.submit()" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Semua Thn</option>
                                <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2027" {{ request('year') == '2027' ? 'selected' : '' }}>2027</option>
                            </select>
                        </div>
                        <div class="w-full md:w-40">
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
                            @if(request()->anyFilled(['search', 'status', 'month', 'year']))
                                <a href="{{ route('invoices.index') }}" class="ml-2 text-sm text-indigo-600 hover:underline">Reset</a>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Periode</th>
                                    <th class="px-4 py-3">Penghuni / Kamar</th>
                                    <th class="px-4 py-3">Total Tagihan</th>
                                    <th class="px-4 py-3">Jatuh Tempo</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">INV-{{ $invoice->id }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $invoice->month }} / {{ $invoice->year }}</td>
                                        <td class="px-4 py-3">
                                            <div class="font-bold text-gray-900 dark:text-gray-100">{{ $invoice->tenant->name ?? 'Dihapus' }}</div>
                                            <div class="text-xs text-gray-500">Kamar {{ $invoice->room->room_number ?? 'Dihapus' }}</div>
                                        </td>
                                        <td class="px-4 py-3 font-bold text-indigo-600">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-red-600 font-medium">{{ $invoice->due_date->format('d/m/Y') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded text-xs font-semibold
                                                @if($invoice->status->value === 'paid') bg-green-100 text-green-700
                                                @elseif($invoice->status->value === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($invoice->status->value === 'overdue') bg-red-100 text-red-700
                                                @else bg-gray-100 text-gray-700 @endif
                                            ">
                                                {{ $invoice->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <a href="{{ route('invoices.show', $invoice) }}" class="text-blue-600 hover:underline">Detail</a>
                                            <a href="{{ route('invoices.edit', $invoice) }}" class="text-indigo-600 hover:underline">Edit Biaya</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data tagihan ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $invoices->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
