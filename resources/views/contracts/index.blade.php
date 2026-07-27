<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Kontrak Sewa') }}
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
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Kontrak</h3>
                        <a href="{{ route('contracts.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            + Buat Kontrak Baru
                        </a>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('contracts.index') }}" class="mb-6 flex flex-col md:flex-row gap-4" x-data x-ref="form">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama penghuni atau nomor kamar..."
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
                            @if(request()->anyFilled(['search', 'status']))
                                <a href="{{ route('contracts.index') }}" class="ml-2 text-sm text-indigo-600 hover:underline">Reset</a>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3">ID</th>
                                    <th class="px-4 py-3">Penghuni</th>
                                    <th class="px-4 py-3">Kamar</th>
                                    <th class="px-4 py-3">Periode Sewa</th>
                                    <th class="px-4 py-3">Harga Sewa</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                            <span class="px-2 py-1 rounded text-xs font-semibold
                                                @if($contract->status->value === 'active') bg-green-100 text-green-700
                                                @elseif($contract->status->value === 'ended') bg-gray-100 text-gray-700
                                                @else bg-red-100 text-red-700 @endif
                                            ">
                                                {{ $contract->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <a href="{{ route('contracts.show', $contract) }}" class="text-blue-600 hover:underline">Detail</a>
                                            <a href="{{ route('contracts.edit', $contract) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <button type="button"
                                                    @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('contracts.destroy', $contract) }}', name: 'kontrak ini' })"
                                                    class="text-red-600 hover:underline">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data kontrak ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $contracts->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
