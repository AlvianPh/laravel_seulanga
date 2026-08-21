<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Tagihan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

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
                                <x-ui.select name="month" class="px-2 py-1 text-sm">
                                    @for($m=1; $m<=12; $m++)
                                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                                    @endfor
                                </x-ui.select>
                                <x-ui.select name="year" class="px-2 py-1 text-sm">
                                    <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                    <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                                </x-ui.select>
                                <x-ui.button type="submit">
                                    + Generate Manual
                                </x-ui.button>
                            </div>
                        </form>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('invoices.index') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-center" x-data x-ref="form">
                        <div class="flex-1 w-full">
                            <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama penghuni..." />
                        </div>
                        <div class="w-full md:w-32">
                            <x-ui.select name="month" @change="$refs.form.submit()">
                                <option value="">Semua Bln</option>
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                                @endfor
                            </x-ui.select>
                        </div>
                        <div class="w-full md:w-32">
                            <x-ui.select name="year" @change="$refs.form.submit()">
                                <option value="">Semua Thn</option>
                                <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                                <option value="2027" {{ request('year') == '2027' ? 'selected' : '' }}>2027</option>
                            </x-ui.select>
                        </div>
                        <div class="w-full md:w-40">
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
                            @if(request()->anyFilled(['search', 'status', 'month', 'year']))
                                <x-ui.button href="{{ route('invoices.index') }}" variant="secondary">Reset</x-ui.button>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <x-ui.table-wrapper>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Periode</th>
                                <th class="px-4 py-3">Penghuni / Kamar</th>
                                <th class="px-4 py-3">Total Tagihan</th>
                                <th class="px-4 py-3">Jatuh Tempo</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </x-slot>
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
                                            <x-ui.badge :status="$invoice->status">{{ $invoice->status->label() }}</x-ui.badge>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <x-ui.button size="sm" variant="secondary" href="{{ route('invoices.show', $invoice) }}">Detail</x-ui.button>
                                            <x-ui.button size="sm" variant="warning" href="{{ route('invoices.edit', $invoice) }}">Edit Biaya</x-ui.button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data tagihan ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                    </x-ui.table-wrapper>

                    <div class="mt-4">
                        {{ $invoices->links() }}
                    </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
