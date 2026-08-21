<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen User') }}
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

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">Daftar User</h3>
                        <x-ui.button href="{{ route('users.create') }}">
                            + Tambah User
                        </x-ui.button>
                    </div>

                    <x-ui.table-wrapper>
                        <x-slot name="header">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3">Aksi</th>
                            </tr>
                        </x-slot>
                            @foreach ($users as $user)
                                <tr class="border-b dark:border-gray-600">
                                    <td class="px-4 py-3">{{ $user->name }}</td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded text-xs font-semibold
                                            {{ $user->role->value === 'owner' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                            {{ $user->role->label() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 space-x-2">
                                        @if (auth()->id() !== $user->id)
                                            <x-ui.button size="sm" variant="warning" href="{{ route('users.edit', $user) }}">Edit</x-ui.button>
                                            <x-ui.button size="sm" variant="danger" type="button"
                                                    @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('users.destroy', $user) }}', name: 'User {{ addslashes($user->name) }}' })">Hapus</x-ui.button>
                                        @else
                                            <span class="text-gray-400 text-xs">Akun Anda</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                    </x-ui.table-wrapper>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
