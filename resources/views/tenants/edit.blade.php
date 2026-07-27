<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Penghuni: ') . $tenant->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('tenants.update', $tenant) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Dasar -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Informasi Pribadi</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name', $tenant->name) }}" required
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                                <input type="text" name="nik" value="{{ old('nik', $tenant->nik) }}" required minlength="16" maxlength="16" pattern="\d{16}"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono">
                                <p class="text-xs text-gray-500 mt-1">Hanya angka, tepat 16 digit.</p>
                                @error('nik') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <select name="gender" required class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->value }}" {{ old('gender', $tenant->gender->value) === $gender->value ? 'selected' : '' }}>
                                            {{ $gender->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir</label>
                                <input type="date" name="birth_date" value="{{ old('birth_date', $tenant->birth_date ? $tenant->birth_date->format('Y-m-d') : '') }}"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('birth_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Kontak & Alamat -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Kontak & Alamat</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP <span class="text-red-500">*</span></label>
                                <input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" required placeholder="08..."
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $tenant->email) }}"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Asal</label>
                                <textarea name="address" rows="3"
                                          class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('address', $tenant->address) }}</textarea>
                                @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 border-t pt-6">
                        <!-- Dokumen -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Ganti Dokumen Lampiran</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ganti Foto KTP (Max 2MB)</label>
                                <input type="file" name="ktp_photo" accept="image/jpeg,image/png,image/webp"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti.</p>
                                @error('ktp_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Ganti Foto Profil (Max 2MB)</label>
                                <input type="file" name="tenant_photo" accept="image/jpeg,image/png,image/webp"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengganti.</p>
                                @error('tenant_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Kontak Darurat -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Kontak Darurat</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kontak Darurat</label>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('emergency_contact_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP Darurat</label>
                                <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}" placeholder="08..."
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('emergency_contact_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700">
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('tenants.index') }}" class="px-6 py-2 bg-gray-200 text-gray-700 font-semibold rounded hover:bg-gray-300">
                            Batal
                        </a>
                    </div>
                </form>

                <!-- Kelola File Eksisting -->
                <div class="mt-12 border-t pt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Dokumen Eksisting</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Foto KTP -->
                        <div class="border rounded p-4 dark:border-gray-700">
                            <h4 class="font-medium mb-2 text-gray-700 dark:text-gray-300">Foto KTP</h4>
                            @if($tenant->ktp_photo_path)
                                <img src="{{ Storage::url($tenant->ktp_photo_path) }}" class="w-full h-48 object-cover rounded mb-3" alt="Foto KTP">
                                <button type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.ktp.destroy', $tenant) }}', name: 'foto KTP ini' })" class="text-sm px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">Hapus KTP</button>
                            @else
                                <p class="text-sm text-gray-500 italic">Belum ada foto KTP.</p>
                            @endif
                        </div>

                        <!-- Foto Profil -->
                        <div class="border rounded p-4 dark:border-gray-700">
                            <h4 class="font-medium mb-2 text-gray-700 dark:text-gray-300">Foto Profil</h4>
                            @if($tenant->tenant_photo_path)
                                <img src="{{ Storage::url($tenant->tenant_photo_path) }}" class="w-full h-48 object-cover rounded mb-3" alt="Foto Profil">
                                <button type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.photo.destroy', $tenant) }}', name: 'foto profil ini' })" class="text-sm px-3 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200">Hapus Profil</button>
                            @else
                                <p class="text-sm text-gray-500 italic">Belum ada foto profil.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
