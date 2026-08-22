<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Kamar') }}
        </h2>
    </x-slot>

    <div class="py-6 sm:py-8" x-data="{ viewMode: 'grid' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                    @if (session('success'))
                        <x-ui.alert type="success">
                            {{ session('success') }}
                        </x-ui.alert>
                    @endif

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Kamar</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola denah kamar, status keterisian, dan harga sewa</p>
                        </div>
                        <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                            <!-- View Mode Toggle (Grid vs Table) -->
                            <div class="inline-flex items-center p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl border border-gray-200/80 dark:border-gray-600 shadow-xs">
                                <button type="button" 
                                        @click="viewMode = 'grid'" 
                                        :class="viewMode === 'grid' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                                    Kartu Denah
                                </button>
                                <button type="button" 
                                        @click="viewMode = 'table'" 
                                        :class="viewMode === 'table' ? 'bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 font-bold shadow-xs' : 'text-gray-500 dark:text-gray-400 hover:text-gray-800'"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                    Tabel List
                                </button>
                            </div>

                            <x-ui.button href="{{ route('rooms.create') }}" variant="primary" size="sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Tambah Kamar
                            </x-ui.button>
                        </div>
                    </div>

                    <!-- Toolbar Filter -->
                    <form method="GET" action="{{ route('rooms.index') }}" class="mb-6 bg-gray-50/80 dark:bg-gray-900/50 p-4 rounded-2xl border border-gray-200/80 dark:border-gray-700/70 flex flex-wrap gap-4 items-end">
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Cari Nomor Kamar / Lantai</label>
                            <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Ketik nomor kamar..."
                                   class="text-sm" />
                        </div>

                        <div class="w-full sm:w-48">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Tipe Kamar</label>
                            <x-ui.select name="room_type_id" class="text-sm">
                                <option value="">Semua Tipe</option>
                                @foreach($roomTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('room_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>

                        <div class="w-full sm:w-44">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1.5">Status Kamar</label>
                            <x-ui.select name="status" class="text-sm">
                                <option value="">Semua Status</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                        {{ $status->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                        </div>

                        <div class="flex gap-2 w-full sm:w-auto">
                            <x-ui.button type="submit" variant="primary" size="sm" class="flex-1 sm:flex-none">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                Filter
                            </x-ui.button>
                            @if(request()->hasAny(['search', 'room_type_id', 'status']))
                                <x-ui.button href="{{ route('rooms.index') }}" variant="secondary" size="sm">
                                    Reset
                                </x-ui.button>
                            @endif
                        </div>
                    </form>

                    <!-- MODE 1: VISUAL ROOM GRID (CARD MAP) -->
                    <div x-show="viewMode === 'grid'" x-cloak>
                        @if($rooms->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
                            @foreach ($rooms as $room)
                                @php
                                    $primaryPhoto = $room->photos->where('is_primary', true)->first() ?? $room->photos->first();
                                    $activeContract = $room->contracts->first();
                                @endphp
                                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200/80 dark:border-gray-700/70 overflow-hidden shadow-xs hover:shadow-md transition-all duration-200 flex flex-col justify-between group">
                                    <!-- Card Header Image & Badges -->
                                    <div class="relative h-40 bg-gray-100 dark:bg-gray-900 overflow-hidden">
                                        @if($primaryPhoto)
                                            <img src="{{ Storage::url($primaryPhoto->file_path) }}" alt="Foto Kamar" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500">
                                                <svg class="w-10 h-10 mb-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4h2a1 1 0 011 1v4h-4v-4a1 1 0 011-1z"></path></svg>
                                                <span class="text-xs">Tanpa Foto</span>
                                            </div>
                                        @endif

                                        <!-- Floor & Status Badges -->
                                        <div class="absolute top-3 left-3">
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-bold bg-white/90 dark:bg-gray-900/90 backdrop-blur-md text-gray-800 dark:text-gray-200 shadow-xs border border-white/20">
                                                Lt. {{ $room->floor }}
                                            </span>
                                        </div>
                                        <div class="absolute top-3 right-3">
                                            <x-ui.badge :status="$room->status">{{ $room->status->label() }}</x-ui.badge>
                                        </div>

                                        <!-- Price Tag Overlay -->
                                        <div class="absolute bottom-2.5 left-3">
                                            <span class="px-2.5 py-1 rounded-xl text-xs font-extrabold bg-gray-900/80 backdrop-blur-md text-white shadow-xs">
                                                Rp {{ number_format($room->monthly_price, 0, ',', '.') }}<span class="text-[10px] font-normal text-gray-300">/bln</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Card Content -->
                                    <div class="p-4 flex-1 flex flex-col justify-between">
                                        <div>
                                            <div class="flex items-center justify-between mb-1.5">
                                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">Kamar {{ $room->room_number }}</h4>
                                                <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $room->roomType?->name ?? 'Standard' }}</span>
                                            </div>

                                            <!-- Occupant Info or Empty State -->
                                            <div class="py-2.5 px-3 rounded-xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700/60 mb-3">
                                                @if($activeContract && $activeContract->tenant)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 flex items-center justify-center text-[10px] font-bold">
                                                            {{ substr($activeContract->tenant->name, 0, 1) }}
                                                        </div>
                                                        <div class="min-w-0 flex-1">
                                                            <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $activeContract->tenant->name }}</p>
                                                            <p class="text-[10px] text-gray-400 truncate">Hingga: {{ \Carbon\Carbon::parse($activeContract->end_date)->format('d M Y') }}</p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        <span>Kamar Siap Disewa</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Actions Footer -->
                                        <div class="pt-3 border-t border-gray-100 dark:border-gray-700/60 flex items-center justify-between gap-1.5">
                                            <x-ui.button size="sm" variant="secondary" href="{{ route('rooms.show', $room) }}" class="flex-1">
                                                Detail
                                            </x-ui.button>
                                            <x-ui.button size="sm" variant="secondary" href="{{ route('rooms.edit', $room) }}" title="Edit">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </x-ui.button>
                                            <x-ui.button size="sm" variant="danger" type="button"
                                                    @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('rooms.destroy', $room) }}', name: 'Kamar {{ $room->room_number }}', softDelete: true })"
                                                    title="Hapus">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </x-ui.button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @else
                            <div class="py-12 text-center">
                                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700/60 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4h2a1 1 0 011 1v4h-4v-4a1 1 0 011-1z"></path></svg>
                                </div>
                                <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Belum Ada Kamar Sesuai Filter</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Coba sesuaikan kata kunci pencarian atau tambah kamar baru.</p>
                            </div>
                        @endif
                    </div>

                    <!-- MODE 2: TABLE LIST -->
                    <div x-show="viewMode === 'table'" x-cloak>
                        <x-ui.table-wrapper>
                            <x-slot name="header">
                                <tr>
                                    <th class="px-4 py-3.5">Foto</th>
                                    <th class="px-4 py-3.5">Nomor Kamar</th>
                                    <th class="px-4 py-3.5">Lantai</th>
                                    <th class="px-4 py-3.5">Tipe</th>
                                    <th class="px-4 py-3.5">Harga/Bulan</th>
                                    <th class="px-4 py-3.5">Status</th>
                                    <th class="px-4 py-3.5 text-right">Aksi</th>
                                </tr>
                            </x-slot>
                                    @forelse ($rooms as $room)
                                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                            <td class="px-4 py-3">
                                                @php
                                                    $primaryPhoto = $room->photos->where('is_primary', true)->first() ?? $room->photos->first();
                                                @endphp
                                                @if($primaryPhoto)
                                                    <img src="{{ Storage::url($primaryPhoto->file_path) }}" alt="Foto Kamar" class="w-12 h-12 object-cover rounded-xl shadow-xs">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-100 dark:bg-gray-700/80 rounded-xl flex items-center justify-center text-gray-400 text-xs font-medium">
                                                        No Pic
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 font-bold text-base text-gray-900 dark:text-white">{{ $room->room_number }}</td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">Lantai {{ $room->floor }}</td>
                                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 font-medium">{{ $room->roomType?->name ?? '-' }}</td>
                                            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">Rp {{ number_format($room->monthly_price, 0, ',', '.') }}</td>
                                            <td class="px-4 py-3">
                                                <x-ui.badge :status="$room->status">{{ $room->status->label() }}</x-ui.badge>
                                            </td>
                                            <td class="px-4 py-3 text-right space-x-1.5">
                                                <x-ui.button size="sm" variant="secondary" href="{{ route('rooms.show', $room) }}">Detail</x-ui.button>
                                                <x-ui.button size="sm" variant="secondary" href="{{ route('rooms.edit', $room) }}">Edit</x-ui.button>
                                                <x-ui.button size="sm" variant="danger" type="button"
                                                        @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('rooms.destroy', $room) }}', name: 'Kamar {{ $room->room_number }}', softDelete: true })">
                                                    Hapus
                                                </x-ui.button>
                                            </td>
                                        </tr>
                                    @empty
                                        <x-ui.empty-state colspan="7" icon="room" title="Belum Ada Data Kamar" description="Tambahkan unit kamar baru untuk mulai mengelola hunian kost." action-text="Tambah Kamar" action-url="{{ route('rooms.create') }}" />
                                    @endforelse
                        </x-ui.table-wrapper>
                    </div>

                    <div class="mt-6">
                        {{ $rooms->links() }}
                    </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
