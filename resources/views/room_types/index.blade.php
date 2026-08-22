<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tipe Kamar') }}
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

        <x-ui.card>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tipe Kamar</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola kategori tipe kamar dan harga dasar rekomendasi</p>
                </div>
                <x-ui.button href="{{ route('room_types.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Tipe
                </x-ui.button>
            </div>

            @if ($roomTypes->isEmpty())
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700/60 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1v1H9V7zm5 0h1v1h-1V7zm-5 4h1v1H9v-1zm5 0h1v1h-1v-1zm-3 4h2a1 1 0 011 1v4h-4v-4a1 1 0 011-1z"></path></svg>
                    </div>
                    <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200">Belum Ada Data Tipe Kamar</h4>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Tambahkan tipe kamar seperti Standard, VIP, atau Suite.</p>
                </div>
            @else
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Nama Tipe</th>
                            <th class="px-4 py-3.5">Deskripsi</th>
                            <th class="px-4 py-3.5">Harga Rekomendasi</th>
                            <th class="px-4 py-3.5 text-center">Total Kamar</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                    @foreach ($roomTypes as $roomType)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">
                                {{ $roomType->name }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300">
                                {{ $roomType->description ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-gray-900 dark:text-white">
                                @if ($roomType->default_price)
                                    Rp {{ number_format($roomType->default_price, 0, ',', '.') }}
                                @else
                                    <span class="text-gray-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $roomType->rooms_count > 0 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $roomType->rooms_count }} kamar
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                <x-ui.button size="sm" variant="secondary" href="{{ route('room_types.edit', $roomType) }}">
                                    Edit
                                </x-ui.button>
                                <x-ui.button size="sm" variant="danger" type="button"
                                        @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('room_types.destroy', $roomType) }}', name: 'tipe kamar {{ addslashes($roomType->name) }}' })">
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
