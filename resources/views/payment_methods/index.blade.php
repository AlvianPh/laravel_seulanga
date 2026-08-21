<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Metode Pembayaran') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="mb-6 border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('payment_methods.index') }}" class="{{ request()->routeIs('payment_methods.*') ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                        Metode Pembayaran
                    </a>
                    <a href="{{ route('bank_accounts.index') }}" class="{{ request()->routeIs('bank_accounts.*') ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }} whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium">
                        Bank / Rekening
                    </a>
                </nav>
            </div>

            <x-ui.card>
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Daftar Metode Pembayaran</h3>
                        <x-ui.button href="{{ route('payment_methods.create') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Metode
                        </x-ui.button>
                    </div>

                    @if ($methods->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8 italic">Belum ada Metode Pembayaran.</p>
                    @else
                        <x-ui.table-wrapper>
                            <x-slot name="header">
                                <tr class="border-b dark:border-gray-700 text-left">
                                    <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Nama Metode</th>
                                    
                                    
                                    <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Digunakan</th>
                                    <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                </tr>
                            </x-slot>
                                    @foreach ($methods as $method)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">
                                                {{ $method->name }}
                                            </td>
                                            <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">
                                                {{ $method->description ?? '-' }}
                                            </td>
                                            <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">
                                                @if ($method->default_price)
                                                    Rp {{ number_format($method->default_price, 0, ',', '.') }}
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 pr-4 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $method->payments_count > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                    {{ $method->payments_count }} kamar
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <div class="flex items-center gap-2">
                                                    <x-ui.button size="sm" variant="warning" href="{{ route('payment_methods.edit', $method) }}">
                                                        Edit
                                                    </x-ui.button>
                                                    <x-ui.button size="sm" variant="danger" type="button"
                                                            @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('payment_methods.destroy', $method) }}', name: 'Metode Pembayaran {{ addslashes($method->name) }}' })">
                                                        Hapus
                                                    </x-ui.button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                        </x-ui.table-wrapper>
                    @endif
            </x-ui.card>

        </div>
    </div>
</x-app-layout>
