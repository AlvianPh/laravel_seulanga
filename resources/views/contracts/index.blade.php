<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Kontrak Sewa') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <x-ui.card>

                @if (session('success'))
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif
                @if ($errors->any())
                    <x-ui.alert type="error">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-ui.alert>
                @endif

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Kontrak Sewa</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola perjanjian sewa kamar, tanggal jatuh tempo, dan deposit</p>
                    </div>
                    <x-ui.button href="{{ route('contracts.create') }}" variant="primary" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Kontrak Baru
                    </x-ui.button>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('contracts.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3 items-center">
                    <div class="flex-1 w-full">
                        <x-ui.input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama penghuni atau kamar..." class="text-sm" />
                    </div>
                    <div class="w-full sm:w-44">
                        <x-ui.select name="status" class="text-sm">
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
                            Filter
                        </x-ui.button>
                        @if(request('search') || request('status'))
                            <x-ui.button href="{{ route('contracts.index') }}" variant="secondary" size="sm">Reset</x-ui.button>
                        @endif
                    </div>
                </form>

                <!-- Table -->
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">ID</th>
                            <th class="px-4 py-3.5">Penghuni</th>
                            <th class="px-4 py-3.5">Kamar</th>
                            <th class="px-4 py-3.5">Periode Sewa</th>
                            <th class="px-4 py-3.5">Harga Sewa</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                            @forelse ($contracts as $contract)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3.5 font-mono text-xs text-gray-500 dark:text-gray-400">#{{ $contract->id }}</td>
                                    <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">{{ $contract->tenant->name ?? 'Dihapus' }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300 font-medium">Kamar {{ $contract->room->room_number ?? 'Dihapus' }}</td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">
                                        {{ $contract->start_date->format('d M Y') }} - {{ $contract->end_date->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3.5 font-semibold text-gray-900 dark:text-white">Rp {{ number_format($contract->rent_price, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5">
                                        <x-ui.badge :status="$contract->status">{{ $contract->status->label() }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3.5 text-right space-x-1.5">
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('contracts.show', $contract) }}">Detail</x-ui.button>
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('contracts.edit', $contract) }}">Edit</x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data kontrak ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                </x-ui.table-wrapper>

                <div class="mt-6">
                    {{ $contracts->links() }}
                </div>

        </x-ui.card>
    </div>
</x-app-layout>
