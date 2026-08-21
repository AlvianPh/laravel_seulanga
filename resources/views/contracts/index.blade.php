<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Kontrak Sewa') }}
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
                    @if ($errors->any())
                        <x-ui.alert type="error">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </x-ui.alert>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Kontrak</h3>
                        <x-ui.button href="{{ route('contracts.create') }}">
                            + Buat Kontrak Baru
                        </x-ui.button>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('contracts.index') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-center" x-data x-ref="form">
                        <div class="flex-1 w-full">
                            <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama penghuni atau nomor kamar..." />
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
                            @if(request()->anyFilled(['search', 'status']))
                                <x-ui.button href="{{ route('contracts.index') }}" variant="secondary">Reset</x-ui.button>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <x-ui.table-wrapper>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3">ID</th>
                                <th class="px-4 py-3">Penghuni</th>
                                <th class="px-4 py-3">Kamar</th>
                                <th class="px-4 py-3">Periode Sewa</th>
                                <th class="px-4 py-3">Harga Sewa</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </x-slot>
                                @forelse ($contracts as $contract)
                                    <tr class="border-b dark:border-gray-600">
                                        <td class="px-4 py-3 font-mono text-xs text-gray-500">#{{ $contract->id }}</td>
                                        <td class="px-4 py-3 font-semibold">{{ $contract->tenant->name ?? 'Dihapus' }}</td>
                                        <td class="px-4 py-3 font-bold text-indigo-600">{{ $contract->room->room_number ?? 'Dihapus' }}</td>
                                        <td class="px-4 py-3">
                                            {{ $contract->start_date->format('d/m/Y') }} - {{ $contract->end_date->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3">Rp {{ number_format($contract->rent_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">
                                            <x-ui.badge :status="$contract->status">{{ $contract->status->label() }}</x-ui.badge>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <x-ui.button size="sm" variant="secondary" href="{{ route('contracts.show', $contract) }}">Detail</x-ui.button>
                                            <x-ui.button size="sm" variant="warning" href="{{ route('contracts.edit', $contract) }}">Edit</x-ui.button>
                                            <x-ui.button size="sm" variant="danger" type="button"
                                                    @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('contracts.destroy', $contract) }}', name: 'kontrak ini' })">Hapus</x-ui.button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data kontrak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                    </x-ui.table-wrapper>

                    <div class="mt-4">
                        {{ $contracts->links() }}
                    </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
