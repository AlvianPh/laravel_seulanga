<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Fasilitas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Daftar Fasilitas</h3>
                        <a href="{{ route('facilities.create') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Fasilitas
                        </a>
                    </div>

                    @if ($facilities->isEmpty())
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8 italic">Belum ada fasilitas.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b dark:border-gray-700 text-left">
                                        <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Nama Fasilitas</th>
                                        <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400">Ikon</th>
                                        <th class="pb-3 pr-4 font-semibold text-gray-600 dark:text-gray-400 text-center">Digunakan</th>
                                        <th class="pb-3 font-semibold text-gray-600 dark:text-gray-400">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach ($facilities as $facility)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                            <td class="py-3 pr-4 font-medium text-gray-900 dark:text-gray-100">
                                                {{ $facility->name }}
                                            </td>
                                            <td class="py-3 pr-4 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                                {{ $facility->icon ?? '-' }}
                                            </td>
                                            <td class="py-3 pr-4 text-center">
                                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $facility->rooms_count > 0 ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                                    {{ $facility->rooms_count }} kamar
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('facilities.edit', $facility) }}"
                                                       class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/40 dark:text-yellow-300 rounded hover:bg-yellow-200 transition-colors">
                                                        Edit
                                                    </a>
                                                    <button type="button"
                                                            @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('facilities.destroy', $facility) }}', name: 'fasilitas {{ addslashes($facility->name) }}' })"
                                                            class="text-xs px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300 rounded hover:bg-red-200 transition-colors">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
