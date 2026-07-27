<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Penghuni') }}
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
                        <h3 class="text-lg font-semibold">Daftar Penghuni</h3>
                        <a href="{{ route('tenants.create') }}"
                           class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                            + Tambah Penghuni
                        </a>
                    </div>

                    <!-- Filter & Search -->
                    <form method="GET" action="{{ route('tenants.index') }}" class="mb-6 flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari nama atau NIK..."
                                   class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500">
                                Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('tenants.index') }}" class="ml-2 text-sm text-indigo-600 hover:underline">Reset</a>
                            @endif
                        </div>
                    </form>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-3">Foto</th>
                                    <th class="px-4 py-3">Nama Lengkap</th>
                                    <th class="px-4 py-3">NIK</th>
                                    <th class="px-4 py-3">No. HP</th>
                                    <th class="px-4 py-3">Gender</th>
                                    <th class="px-4 py-3">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
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
                                            <a href="{{ route('tenants.show', $tenant) }}" class="text-blue-600 hover:underline">Detail</a>
                                            <a href="{{ route('tenants.edit', $tenant) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <button type="button"
                                                    @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.destroy', $tenant) }}', name: 'Penghuni {{ addslashes($tenant->name) }}', softDelete: true })"
                                                    class="text-red-600 hover:underline">Hapus</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                            Tidak ada data penghuni ditemukan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $tenants->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
