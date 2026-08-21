<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Penghuni: ') . $tenant->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                @if (session('success'))
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
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
                                <x-ui.input type="text" name="name" value="{{ old('name', $tenant->name) }}" required />
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                                <x-ui.input type="text" name="nik" value="{{ old('nik', $tenant->nik) }}" required minlength="16" maxlength="16" pattern="\d{16}"
                                       class="font-mono" />
                                <p class="text-xs text-gray-500 mt-1">Hanya angka, tepat 16 digit.</p>
                                @error('nik') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <x-ui.select name="gender" required>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->value }}" {{ old('gender', $tenant->gender->value) === $gender->value ? 'selected' : '' }}>
                                            {{ $gender->label() }}
                                        </option>
                                    @endforeach
                                </x-ui.select>
                                @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir</label>
                                <x-ui.input type="date" name="birth_date" value="{{ old('birth_date', $tenant->birth_date ? $tenant->birth_date->format('Y-m-d') : '') }}" />
                                @error('birth_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Kontak & Alamat -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Kontak & Alamat</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP <span class="text-red-500">*</span></label>
                                <x-ui.input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" required placeholder="08..." />
                                @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <x-ui.input type="email" name="email" value="{{ old('email', $tenant->email) }}" />
                                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Asal</label>
                                <x-ui.textarea name="address" rows="3">{{ old('address', $tenant->address) }}</x-ui.textarea>
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
                                <x-ui.input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}" />
                                @error('emergency_contact_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP Darurat</label>
                                <x-ui.input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}" placeholder="08..." />
                                @error('emergency_contact_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <x-ui.button type="submit">
                            Simpan Perubahan
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('tenants.index') }}">
                            Batal
                        </x-ui.button>
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
                                <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.ktp.destroy', $tenant) }}', name: 'foto KTP ini' })">Hapus KTP</x-ui.button>
                            @else
                                <p class="text-sm text-gray-500 italic">Belum ada foto KTP.</p>
                            @endif
                        </div>

                        <!-- Foto Profil -->
                        <div class="border rounded p-4 dark:border-gray-700">
                            <h4 class="font-medium mb-2 text-gray-700 dark:text-gray-300">Foto Profil</h4>
                            @if($tenant->tenant_photo_path)
                                <img src="{{ Storage::url($tenant->tenant_photo_path) }}" class="w-full h-48 object-cover rounded mb-3" alt="Foto Profil">
                                <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.photo.destroy', $tenant) }}', name: 'foto profil ini' })">Hapus Profil</x-ui.button>
                            @else
                                <p class="text-sm text-gray-500 italic">Belum ada foto profil.</p>
                            @endif
                        </div>
                    </div>
                </div>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
