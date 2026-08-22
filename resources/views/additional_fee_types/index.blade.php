<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Jenis Denda/Biaya Tambahan') }}
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
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Jenis Denda & Biaya Tambahan</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola komponen biaya insidental seperti Denda Telat Bayar, Tambahan Tamu, Listrik Elektronik Tambahan, dll</p>
                </div>
                <x-ui.button href="{{ route('additional_fee_types.create') }}" variant="primary" size="sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Jenis
                </x-ui.button>
            </div>

            @if ($feeTypes->isEmpty())
                <x-ui.empty-state icon="invoice" title="Belum Ada Jenis Biaya Tambahan" description="Tambahkan jenis denda atau biaya tambahan insidental untuk perhitungan tagihan invoice." action-text="Tambah Jenis Biaya" action-url="{{ route('additional_fee_types.create') }}" />
            @else
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">Nama</th>
                            <th class="px-4 py-3.5">Jenis Perhitungan</th>
                            <th class="px-4 py-3.5">Nilai Default</th>
                            <th class="px-4 py-3.5 text-center">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                    @foreach ($feeTypes as $type)
                        <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                            <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">{{ $type->nama }}</td>
                            <td class="px-4 py-3.5 text-gray-700 dark:text-gray-300">
                                @if ($type->jenis === 'nominal_tetap')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300">Nominal Tetap</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300">Persentase</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 font-semibold text-gray-900 dark:text-white">
                                @if ($type->jenis === 'nominal_tetap')
                                    Rp {{ number_format($type->nilai_default, 0, ',', '.') }}
                                @else
                                    {{ rtrim(rtrim(number_format($type->nilai_default, 2, ',', '.'), '0'), ',') }}%
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-center">
                                @if ($type->is_active)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-4 py-3.5 text-right space-x-1.5">
                                <x-ui.button size="sm" variant="secondary" href="{{ route('additional_fee_types.edit', $type) }}">Edit</x-ui.button>
                                <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('additional_fee_types.destroy', $type) }}', name: 'jenis {{ addslashes($type->nama) }}' })">Hapus</x-ui.button>
                            </td>
                        </tr>
                    @endforeach
                </x-ui.table-wrapper>
                <div class="mt-6">
                    {{ $feeTypes->links() }}
                </div>
            @endif
        </x-ui.card>

    </div>
</x-app-layout>
