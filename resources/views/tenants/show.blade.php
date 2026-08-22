<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profil Penghuni: ') . $tenant->name }}
            </h2>
            <div class="space-x-2">
                <x-ui.button variant="secondary" href="{{ route('tenants.index') }}">Kembali</x-ui.button>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Profil Penghuni: {{ $tenant->name }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Informasi identitas penyewa, dokumen KTP, kontak darurat dan riwayat kontrak</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button variant="primary" size="sm" href="{{ route('tenants.edit', $tenant) }}">Edit Profil</x-ui.button>
                <x-ui.button variant="secondary" size="sm" href="{{ route('tenants.index') }}">Kembali</x-ui.button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Foto & Info Singkat -->
            <div class="lg:col-span-1 space-y-6">
                <x-ui.card class="text-center">
                    @if($tenant->tenant_photo_path)
                        <img src="{{ Storage::url($tenant->tenant_photo_path) }}" class="w-28 h-28 object-cover rounded-full mx-auto mb-4 border-4 border-indigo-100 dark:border-indigo-950/60 shadow-xs" alt="Foto Profil">
                    @else
                        <div class="w-28 h-28 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 rounded-full mx-auto mb-4 flex items-center justify-center text-2xl font-bold border-4 border-indigo-100 dark:border-indigo-900/60 shadow-xs">
                            {{ substr($tenant->name, 0, 2) }}
                        </div>
                    @endif
                    
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $tenant->name }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 font-mono">NIK: {{ $tenant->nik }}</p>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700/60 text-left space-y-2 text-xs">
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $tenant->phone }}</span>
                        </div>
                        <div class="flex items-center text-gray-600 dark:text-gray-300">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <span>{{ $tenant->email ?: '-' }}</span>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Dokumen KTP -->
                <x-ui.card>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Dokumen KTP</h4>
                    @if($tenant->ktp_photo_path)
                        <a href="{{ Storage::url($tenant->ktp_photo_path) }}" target="_blank" class="block overflow-hidden rounded-xl border border-gray-200/80 dark:border-gray-700">
                            <img src="{{ Storage::url($tenant->ktp_photo_path) }}" class="w-full h-auto object-cover hover:scale-105 transition-transform" alt="Foto KTP">
                        </a>
                        <p class="text-[11px] text-center text-gray-400 mt-2">Klik gambar untuk melihat dokumen ukuran penuh</p>
                    @else
                        <div class="bg-gray-50 dark:bg-gray-900/40 rounded-xl h-28 flex items-center justify-center text-gray-400 text-xs border border-dashed border-gray-200 dark:border-gray-700">
                            Belum ada dokumen KTP.
                        </div>
                    @endif
                </x-ui.card>
            </div>

            <!-- Kolom Kanan: Detail & Riwayat -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Detail Lengkap -->
                <x-ui.card>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Informasi Rinci</h4>
                    
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4 text-xs">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Jenis Kelamin</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $tenant->gender->label() }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Tanggal Lahir</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $tenant->birth_date ? $tenant->birth_date->format('d F Y') : '-' }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Alamat Asal KTP</dt>
                            <dd class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">{{ $tenant->address ?: '-' }}</dd>
                        </div>
                    </dl>

                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mt-6 mb-3 border-b border-gray-100 dark:border-gray-700/70 pb-2">Kontak Darurat</h4>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4 text-xs">
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">Nama Kontak</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $tenant->emergency_contact_name ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500 dark:text-gray-400 font-bold uppercase tracking-wider">No. HP Darurat</dt>
                            <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $tenant->emergency_contact_phone ?: '-' }}</dd>
                        </div>
                    </dl>
                </x-ui.card>

                <!-- Riwayat Kontrak -->
                <x-ui.card>
                    <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Riwayat Sewa Kamar</h4>
                    
                    @if($tenant->contracts->isEmpty())
                        <p class="text-gray-400 italic text-xs py-2">Penghuni ini belum memiliki riwayat kontrak penyewaan kamar.</p>
                    @else
                        <x-ui.table-wrapper>
                            <x-slot name="header">
                                <tr>
                                    <th class="px-4 py-3.5">Kamar</th>
                                    <th class="px-4 py-3.5">Mulai</th>
                                    <th class="px-4 py-3.5">Selesai</th>
                                    <th class="px-4 py-3.5">Status</th>
                                </tr>
                            </x-slot>
                            @foreach($tenant->contracts as $contract)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">
                                        Kamar {{ $contract->room ? $contract->room->room_number : 'Dihapus' }}
                                    </td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">{{ $contract->start_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3.5 text-xs text-gray-600 dark:text-gray-300">{{ $contract->end_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3.5">
                                        <x-ui.badge :status="$contract->status">{{ $contract->status->label() }}</x-ui.badge>
                                    </td>
                                </tr>
                            @endforeach
                        </x-ui.table-wrapper>
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
