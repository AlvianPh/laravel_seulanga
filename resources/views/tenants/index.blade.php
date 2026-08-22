<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Penghuni') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <x-ui.card>

                @if (session('success'))
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Penghuni</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftar data diri penyewa dan riwayat hunian</p>
                    </div>
                    <x-ui.button href="{{ route('tenants.create') }}" variant="primary" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Tambah Penghuni
                    </x-ui.button>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('tenants.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3 items-center">
                    <div class="flex-1 w-full">
                        <x-ui.input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama atau NIK penghuni..." class="text-sm" />
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <x-ui.button type="submit" variant="primary" size="sm" class="flex-1 sm:flex-none">
                            Cari
                        </x-ui.button>
                        @if(request('search'))
                            <x-ui.button href="{{ route('tenants.index') }}" variant="secondary" size="sm">Reset</x-ui.button>
                        @endif
                    </div>
                </form>

                <!-- Table -->
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Foto</th>
                            <th class="px-4 py-3.5">Nama Lengkap</th>
                            <th class="px-4 py-3.5">NIK</th>
                            <th class="px-4 py-3.5">No. HP</th>
                            <th class="px-4 py-3.5">Gender</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                            @forelse ($tenants as $tenant)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3">
                                        @if($tenant->tenant_photo_path)
                                            <img src="{{ Storage::url($tenant->tenant_photo_path) }}" alt="Foto {{ $tenant->name }}" class="w-10 h-10 object-cover rounded-full ring-2 ring-indigo-500/20">
                                        @else
                                            <div class="w-10 h-10 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 rounded-full flex items-center justify-center font-bold text-xs">
                                                {{ substr($tenant->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">{{ $tenant->name }}</td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-gray-500 dark:text-gray-400">{{ $tenant->nik }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $tenant->phone }}</td>
                                    <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">{{ $tenant->gender->label() }}</td>
                                    <td class="px-4 py-3.5 text-right space-x-1.5">
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('tenants.show', $tenant) }}">Detail</x-ui.button>
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('tenants.edit', $tenant) }}">Edit</x-ui.button>
                                        <x-ui.button size="sm" variant="danger" type="button"
                                                @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.destroy', $tenant) }}', name: 'Penghuni {{ addslashes($tenant->name) }}', softDelete: true })">Hapus</x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data penghuni ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                </x-ui.table-wrapper>

                <div class="mt-6">
                    {{ $tenants->links() }}
                </div>

        </x-ui.card>
    </div>
</x-app-layout>
