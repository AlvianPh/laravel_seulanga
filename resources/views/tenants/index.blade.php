<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Penghuni') }}
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
                        <h3 class="text-lg font-semibold">Daftar Penghuni</h3>
                        <x-ui.button href="{{ route('tenants.create') }}">
                            + Tambah Penghuni
                        </x-ui.button>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('tenants.index') }}" class="mb-6 flex flex-col md:flex-row gap-4 items-center">
                        <div class="flex-1 w-full">
                            <x-ui.input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama atau NIK..." />
                        </div>
                        <div class="flex gap-2">
                            <x-ui.button type="submit" variant="secondary">
                                Cari
                            </x-ui.button>
                            @if(request('search'))
                                <x-ui.button href="{{ route('tenants.index') }}" variant="secondary">Reset</x-ui.button>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <x-ui.table-wrapper>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3">Foto</th>
                                <th class="px-4 py-3">Nama Lengkap</th>
                                <th class="px-4 py-3">NIK</th>
                                <th class="px-4 py-3">No. HP</th>
                                <th class="px-4 py-3">Gender</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </x-slot>
                                @forelse ($tenants as $tenant)
                                    <tr class="border-b dark:border-gray-600">
                                        <td class="px-4 py-3">
                                            @if($tenant->tenant_photo_path)
                                                <img src="{{ Storage::url($tenant->tenant_photo_path) }}" alt="Foto {{ $tenant->name }}" class="w-12 h-12 object-cover rounded-full">
                                            @else
                                                <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-gray-400 text-xs">
                                                    Kosong
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-semibold">{{ $tenant->name }}</td>
                                        <td class="px-4 py-3 font-mono text-xs">{{ $tenant->nik }}</td>
                                        <td class="px-4 py-3">{{ $tenant->phone }}</td>
                                        <td class="px-4 py-3">{{ $tenant->gender->label() }}</td>
                                        <td class="px-4 py-3 space-x-2">
                                            <x-ui.button size="sm" variant="secondary" href="{{ route('tenants.show', $tenant) }}">Detail</x-ui.button>
                                            <x-ui.button size="sm" variant="warning" href="{{ route('tenants.edit', $tenant) }}">Edit</x-ui.button>
                                            <x-ui.button size="sm" variant="danger" type="button"
                                                    @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.destroy', $tenant) }}', name: 'Penghuni {{ addslashes($tenant->name) }}', softDelete: true })">Hapus</x-ui.button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data penghuni ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                    </x-ui.table-wrapper>

                    <div class="mt-4">
                        {{ $tenants->links() }}
                    </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
