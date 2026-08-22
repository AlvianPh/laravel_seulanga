<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen User') }}
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
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Akun Pengguna</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola akun staf admin dan hak akses sistem</p>
                    </div>
                    <x-ui.button href="{{ route('users.create') }}" variant="primary" size="sm">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Tambah User
                    </x-ui.button>
                </div>

                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Nama Lengkap</th>
                            <th class="px-4 py-3.5">Alamat Email</th>
                            <th class="px-4 py-3.5 text-center">Hak Akses (Role)</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                        @foreach ($users as $user)
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 flex items-center justify-center font-bold text-xs">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <span>{{ $user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300">{{ $user->email }}</td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $user->role->value === 'owner' ? 'bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/60' : 'bg-sky-50 text-sky-700 dark:bg-sky-950/60 dark:text-sky-300 border border-sky-200/80 dark:border-sky-800/60' }}">
                                        {{ $user->role->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                    @if (auth()->id() !== $user->id)
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('users.edit', $user) }}">Edit</x-ui.button>
                                        <x-ui.button size="sm" variant="danger" type="button"
                                                @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('users.destroy', $user) }}', name: 'User {{ addslashes($user->name) }}' })">Hapus</x-ui.button>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-500 text-xs italic">Akun Anda</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                </x-ui.table-wrapper>
        </x-ui.card>
    </div>
</x-app-layout>
