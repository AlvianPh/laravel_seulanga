<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Metode Pembayaran') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">

        @if (session('success'))
            <x-ui.alert type="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif
        @if (session('error'))
            <x-ui.alert type="error">
                {{ session('error') }}
            </x-ui.alert>
        @endif

        <!-- Sub-navigation Tabs (Modern Pill/Underline Tab) -->
        <div class="flex items-center gap-2 p-1.5 bg-gray-100 dark:bg-gray-800/80 rounded-2xl w-fit border border-gray-200/80 dark:border-gray-700/70">
            <a href="{{ route('payment_methods.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('payment_methods.*') ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                Metode Pembayaran
            </a>
            <a href="{{ route('bank_accounts.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('bank_accounts.*') ? 'bg-white dark:bg-gray-900 text-indigo-600 dark:text-indigo-400 shadow-xs' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900' }}">
                Rekening Bank Kost
            </a>
        </div>

        <x-ui.card>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Metode Pembayaran</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola opsi cara bayar seperti Transfer Bank, Tunai / Cash, QRIS, dll</p>
                </div>
                <x-ui.button href="{{ route('payment_methods.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Metode
                </x-ui.button>
            </div>

            @if ($methods->isEmpty())
                <x-ui.empty-state icon="payment" title="Belum Ada Metode Pembayaran" description="Tambahkan opsi cara bayar seperti Transfer Bank, Tunai / Cash, QRIS, dll." action-text="Tambah Metode" action-url="{{ route('payment_methods.create') }}" />
            @else
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Nama Metode</th>
                            <th class="px-4 py-3.5 text-center">Total Transaksi</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                    @foreach ($methods as $method)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">
                                {{ $method->name }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $method->payments_count > 0 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $method->payments_count }} transaksi
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                <x-ui.button size="sm" variant="secondary" href="{{ route('payment_methods.edit', $method) }}">
                                    Edit
                                </x-ui.button>
                                <x-ui.button size="sm" variant="danger" type="button"
                                        @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('payment_methods.destroy', $method) }}', name: 'Metode Pembayaran {{ addslashes($method->name) }}' })">
                                    Hapus
                                </x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table-wrapper>
            @endif
        </x-ui.card>

    </div>
</x-app-layout>
