<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Kamar') }}
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

                    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                        <h3 class="text-lg font-semibold">Daftar Kamar</h3>
                        <a href="{{ route('rooms.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            + Tambah Kamar
                        </a>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('rooms.index') }}" class="mb-6 flex flex-col md:flex-row gap-4" x-data x-ref="form">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari no kamar..."
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div class="w-full md:w-48">
                            <select name="room_type_id" @change="$refs.form.submit()" class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Semua Tipe</option>
                                @foreach ($roomTypes as $roomType)
                                    <option value="{{ $roomType->id }}" {{ request('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                        {{ $roomType->name }}
                                    </option>
                                @endforeach
                            </select>
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
                            @if(request()->anyFilled(['search', 'room_type_id', 'status']))
                                <a href="{{ route('rooms.index') }}" class="ml-2 text-sm text-indigo-600 hover:underline">Reset</a>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Foto</th>
                                    <th class="px-4 py-3">No. Kamar</th>
                                    <th class="px-4 py-3">Lantai</th>
                                    <th class="px-4 py-3">Tipe</th>
                                    <th class="px-4 py-3">Harga/Bulan</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rooms as $room)
                                    <tr class="border-b dark:border-gray-600">
                                        <td class="px-4 py-3">
                                            @php
                                                $primaryPhoto = $room->photos->where('is_primary', true)->first() ?? $room->photos->first();
                                            @endphp
                                            @if($primaryPhoto)
                                                <img src="{{ Storage::url($primaryPhoto->file_path) }}" alt="Kamar {{ $room->room_number }}" class="w-16 h-16 object-cover rounded">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center text-gray-400 text-xs">
                                                    No Image
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-lg">{{ $room->room_number }}</td>
                                        <td class="px-4 py-3">{{ $room->floor }}</td>
                                        <td class="px-4 py-3">{{ $room->roomType?->name ?? '-' }}</td>
                                        <td class="px-4 py-3">Rp {{ number_format($room->monthly_price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">
                                            <span class="px-2 py-1 rounded text-xs font-semibold
                                                @if($room->status->value === 'available') bg-green-100 text-green-700
                                                @elseif($room->status->value === 'occupied') bg-blue-100 text-blue-700
                                                @else bg-yellow-100 text-yellow-700 @endif
                                            ">
                                                {{ $room->status->label() }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 space-x-2">
                                            <a href="{{ route('rooms.show', $room) }}" class="text-blue-600 hover:underline">Detail</a>
                                            <a href="{{ route('rooms.edit', $room) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('rooms.destroy', $room) }}" class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('Yakin ingin menghapus kamar ini? Semua riwayat yang terhubung mungkin akan error jika belum di-handle.')"
                                                        class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data kamar ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $rooms->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
