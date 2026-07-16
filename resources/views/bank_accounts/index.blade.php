<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Bank/Rekening') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Daftar Rekening Bank</h3>
                        <a href="{{ route('bank_accounts.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                            Tambah Rekening
                        </a>
                    </div>

                    @if ($bankAccounts->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8 italic">Belum ada Rekening.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 text-left">
                                        <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Nama Bank</th>
                                        <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Nomor Rekening</th>
                                        <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Nama Pemilik</th>
                                        <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Status</th>
                                        <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($bankAccounts as $account)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">{{ $account->nama_bank }}</td>
                                            <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $account->nomor_rekening }}</td>
                                            <td class="py-3 pr-4 text-gray-700 dark:text-gray-300">{{ $account->nama_pemilik_rekening }}</td>
                                            <td class="py-3 pr-4 text-center">
                                                @if ($account->is_active)
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">Aktif</span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Nonaktif</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('bank_accounts.edit', $account) }}" class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300 rounded hover:bg-yellow-200 transition-colors">Edit</a>
                                                    <form action="{{ route('bank_accounts.destroy', $account) }}" method="POST" onsubmit="return confirm('Hapus rekening ini?')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-xs px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 rounded hover:bg-red-200 transition-colors">Hapus</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $bankAccounts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
