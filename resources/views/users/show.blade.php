<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail User: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="mt-1">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Role</dt>
                        <dd class="mt-1">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                {{ $user->role->value === 'owner' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->role->label() }}
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="mt-1">{{ $user->created_at->format('d M Y, H:i') }}</dd>
                    </div>
                </dl>

                <div class="mt-6 flex gap-3">
                    <x-ui.button variant="warning" href="{{ route('users.edit', $user) }}">
                        Edit
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('users.index') }}">
                        Kembali
                    </x-ui.button>
                </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
