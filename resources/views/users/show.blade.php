<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail User: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Detail Profil Pengguna</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Informasi akun pengguna dan wewenang peran di dalam sistem</p>
            </div>

            <dl class="space-y-4 text-xs">
                <div>
                    <dt class="font-bold uppercase tracking-wider text-gray-400">Nama Pengguna</dt>
                    <dd class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ $user->name }}</dd>
                </div>
                <div>
                    <dt class="font-bold uppercase tracking-wider text-gray-400">Alamat Email</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->email }}</dd>
                </div>
                <div>
                    <dt class="font-bold uppercase tracking-wider text-gray-400 mb-1">Peran Pengguna (Role)</dt>
                    <dd>
                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                            {{ $user->role->value === 'owner' ? 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300' : 'bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300' }}">
                            {{ $user->role->label() }}
                        </span>
                    </dd>
                </div>
                <div class="border-t border-gray-100 dark:border-gray-700/60 pt-3 text-[11px] text-gray-400">
                    <dt class="font-bold uppercase tracking-wider text-gray-400 mb-0.5">Terdaftar Sejak</dt>
                    <dd>{{ $user->created_at->format('d M Y, H:i') }}</dd>
                </div>
            </dl>

            <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60 mt-6">
                <x-ui.button variant="primary" href="{{ route('users.edit', $user) }}">
                    Edit Pengguna
                </x-ui.button>
                <x-ui.button variant="secondary" href="{{ route('users.index') }}">
                    Kembali
                </x-ui.button>
            </div>

        </x-ui.card>
    </div>
</x-app-layout>
