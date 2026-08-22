<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Fasilitas') }}
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
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Fasilitas Kamar & Kost</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola daftar fasilitas penunjang seperti AC, WiFi, Water Heater, dll</p>
                </div>
                <x-ui.button href="{{ route('facilities.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Tambah Fasilitas
                </x-ui.button>
            </div>

            @if ($facilities->isEmpty())
                <x-ui.empty-state icon="facility" title="Belum Ada Fasilitas" description="Tambahkan daftar fasilitas kamar dan fasilitas umum kost seperti AC, WiFi, Water Heater, dll." action-text="Tambah Fasilitas" action-url="{{ route('facilities.create') }}" />
            @else
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Nama Fasilitas</th>
                            <th class="px-4 py-3.5">Ikon / Kode</th>
                            <th class="px-4 py-3.5 text-center">Total Digunakan</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                    @foreach ($facilities as $facility)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">
                                {{ $facility->name }}
                            </td>
                            <td class="px-4 py-3.5 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                {{ $facility->icon ?? '-' }}
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $facility->rooms_count > 0 ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 border border-indigo-200/80 dark:border-indigo-800/60' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                    {{ $facility->rooms_count }} kamar
                                </span>
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                <x-ui.button size="sm" variant="secondary" href="{{ route('facilities.edit', $facility) }}">
                                    Edit
                                </x-ui.button>
                                <x-ui.button size="sm" variant="danger" type="button"
                                        @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('facilities.destroy', $facility) }}', name: 'fasilitas {{ addslashes($facility->name) }}' })">
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
