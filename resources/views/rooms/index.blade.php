<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Kamar') }}
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

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Kamar</h3>
                        <a href="{{ route('rooms.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            + Tambah Kamar
                        </a>
                    </div>

                    <!-- Toolbar Filter -->
                    <form method="GET" action="{{ route('rooms.index') }}" class="mb-6 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs text-gray-500 dark:text-gray-300 mb-1">Cari Kamar / Lantai</label>
                            <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Contoh: 101, Lantai 1..."
                                   class="text-sm" />
                        </div>

                        <div class="w-48">
                            <label class="block text-xs text-gray-500 dark:text-gray-300 mb-1">Tipe Kamar</label>
                            <x-ui.select name="room_type_id" class="text-sm">
                                <option value="">Semua Tipe</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>

                        <div class="w-40">
                            <label class="block text-xs text-gray-500 dark:text-gray-300 mb-1">Status</label>
                            <x-ui.select name="status" class="text-sm">
                                <option value="">Semua Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>

                        <div class="flex gap-2">
                            <x-ui.button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm rounded hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500">Filter</x-ui.button>
                            @if(request()->hasAny(['search', 'room_type_id', 'status']))
                                <x-ui.button href="{{ route('rooms.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">Reset</x-ui.button>
                            @endif
                        </div>
                    </form>

                    <x-ui.table-wrapper>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3">Foto</th>
                                <th class="px-4 py-3">Nomor Kamar</th>
                                <th class="px-4 py-3">Lantai</th>
                                <th class="px-4 py-3">Tipe</th>
                                <th class="px-4 py-3">Harga/Bulan</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </x-slot>
                                @forelse ($rooms as $room)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="px-4 py-3">
                                            @php
                                                $primaryPhoto = $room->photos->where('is_primary', true)->first() ?? $room->photos->first();
                                            @endphp
                                            @if($primaryPhoto)
                                                <img src="{{ Storage::url($primaryPhoto->file_path) }}" alt="Foto Kamar" class="w-12 h-12 object-cover rounded">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center text-gray-400 text-xs">
                                                    No Pic
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-lg">{{ $room->room_number }}</td>
                                        <td class="px-4 py-3">{{ $room->floor }}</td>
                                        <td class="px-4 py-3">{{ $room->roomType?->name ?? '-' }}</td>
                                        <td class="px-4 py-3">Rp {{ number_format($room->monthly_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">
                                            <x-ui.badge :status="$room->status">{{ $room->status->label() }}</x-ui.badge>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <a href="{{ route('rooms.show', $room) }}" class="text-blue-600 hover:underline">Detail</a>
                                            <a href="{{ route('rooms.edit', $room) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <button type="button"
                                                    @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('rooms.destroy', $room) }}', name: 'Kamar {{ $room->room_number }}', softDelete: true })"
                                                    class="text-red-600 hover:underline">
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                                            Belum ada data kamar.
                                        </td>
                                    </tr>
                                @endforelse
                    </x-ui.table-wrapper>

                    <div class="mt-4">
                        {{ $rooms->links() }}
                    </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
