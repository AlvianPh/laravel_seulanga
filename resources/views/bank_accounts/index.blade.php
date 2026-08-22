<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bank/Rekening') }}
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
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Rekening Bank Kost</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar nomor rekening bank penerima pembayaran untuk dicantumkan pada invoice</p>
                </div>
                <x-ui.button href="{{ route('bank_accounts.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Rekening
                </x-ui.button>
            </div>

            @if ($bankAccounts->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700/60 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Belum Ada Rekening Bank</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tambahkan rekening bank untuk mempermudah transfer sewa penghuni.</p>
                </div>
            @else
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Nama Bank</th>
                            <th class="px-4 py-3.5">Nomor Rekening</th>
                            <th class="px-4 py-3.5">Nama Pemilik</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                    @foreach ($bankAccounts as $account)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">{{ $account->nama_bank }}</td>
                            <td class="px-4 py-3.5 font-mono text-sm text-gray-800 dark:text-gray-200 font-semibold">{{ $account->nomor_rekening }}</td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300 font-medium">{{ $account->nama_pemilik_rekening }}</td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($account->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                <x-ui.button size="sm" variant="secondary" href="{{ route('bank_accounts.edit', $account) }}">Edit</x-ui.button>
                                <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('bank_accounts.destroy', $account) }}', name: 'rekening {{ addslashes($account->nama_bank) }}' })">Hapus</x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table-wrapper>
                <div class="mt-6">
                    {{ $bankAccounts->links() }}
                </div>
            @endif
        </x-ui.card>

    </div>
</x-app-layout>
