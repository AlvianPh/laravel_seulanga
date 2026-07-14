<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Profil Penghuni: ') . $tenant->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('tenants.edit', $tenant) }}" class="px-4 py-2 bg-indigo-600 text-white text-sm rounded hover:bg-indigo-700">Edit Profil</a>
                <a href="{{ route('tenants.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded hover:bg-gray-300">Kembali</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <!-- Kolom Kiri: Foto & Info Singkat -->
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                        @if($tenant->tenant_photo_path)
                            <img src="{{ Storage::url($tenant->tenant_photo_path) }}" class="w-32 h-32 object-cover rounded-full mx-auto mb-4 border-4 border-indigo-100 dark:border-indigo-900" alt="Foto Profil">
                        @else
                            <div class="w-32 h-32 bg-gray-200 dark:bg-gray-700 rounded-full mx-auto mb-4 flex items-center justify-center text-gray-500 text-xl font-bold">
                                {{ substr($tenant->name, 0, 2) }}
                            </div>
                        @endif
                        
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $tenant->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 font-mono">{{ $tenant->nik }}</p>
                        
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 text-left">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                {{ $tenant->phone }}
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-300 mb-2">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                {{ $tenant->email ?: '-' }}
                            </div>
                        </div>
                    </div>

                    <!-- Dokumen KTP -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Dokumen KTP</h3>
                        @if($tenant->ktp_photo_path)
                            <a href="{{ Storage::url($tenant->ktp_photo_path) }}" target="_blank">
                                <img src="{{ Storage::url($tenant->ktp_photo_path) }}" class="w-full h-auto object-cover rounded shadow hover:opacity-90" alt="Foto KTP">
                            </a>
                            <p class="text-xs text-center text-gray-500 mt-2">Klik gambar untuk melihat penuh</p>
                        @else
                            <div class="bg-gray-100 dark:bg-gray-700 rounded h-32 flex items-center justify-center text-gray-500 text-sm">
                                Belum ada foto KTP.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kolom Kanan: Detail & Riwayat -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Detail Lengkap -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Informasi Detail</h3>
                        
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Jenis Kelamin</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $tenant->gender->label() }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Tanggal Lahir</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $tenant->birth_date ? $tenant->birth_date->format('d F Y') : '-' }}</dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Alamat Asal</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $tenant->address ?: '-' }}</dd>
                            </div>
                        </dl>

                        <h4 class="text-md font-medium text-gray-900 dark:text-gray-100 mt-6 mb-3 border-b pb-2">Kontak Darurat</h4>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6 text-sm">
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">Nama Kontak</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $tenant->emergency_contact_name ?: '-' }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500 dark:text-gray-400 font-medium">No. HP Darurat</dt>
                                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $tenant->emergency_contact_phone ?: '-' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Riwayat Kontrak -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b pb-2">Riwayat Sewa Kamar</h3>
                        
                        @if($tenant->contracts->isEmpty())
                            <p class="text-gray-500 italic text-sm">Penghuni ini belum memiliki riwayat kontrak penyewaan kamar.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th class="px-4 py-2">Kamar</th>
                                            <th class="px-4 py-2">Tanggal Mulai</th>
                                            <th class="px-4 py-2">Tanggal Selesai</th>
                                            <th class="px-4 py-2">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tenant->contracts as $contract)
                                            <tr class="border-b dark:border-gray-600">
                                                <td class="px-4 py-3 font-medium text-indigo-600">
                                                    {{ $contract->room ? $contract->room->room_number : 'Kamar Dihapus' }}
                                                </td>
                                                <td class="px-4 py-3">{{ $contract->start_date->format('d M Y') }}</td>
                                                <td class="px-4 py-3">{{ $contract->end_date->format('d M Y') }}</td>
                                                <td class="px-4 py-3">
                                                    {{ $contract->status->label() }}
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
    </div>
</x-app-layout>
