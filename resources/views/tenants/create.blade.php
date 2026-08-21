<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Penghuni Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>

                <form method="POST" action="{{ route('tenants.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Info Dasar -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Informasi Pribadi</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                                <x-ui.input type="text" name="name" value="{{ old('name') }}" required />
                                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">NIK (16 Digit) <span class="text-red-500">*</span></label>
                                <x-ui.input type="text" name="nik" value="{{ old('nik') }}" required minlength="16" maxlength="16" pattern="\d{16}"
                                       class="font-mono" />
                                <p class="text-xs text-gray-500 mt-1">Hanya angka, tepat 16 digit.</p>
                                @error('nik') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                <x-ui.select name="gender" required>
                                    <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                    @foreach ($genders as $gender)
                                        <option value="{{ $gender->value }}" {{ old('gender') === $gender->value ? 'selected' : '' }}>
                                            {{ $gender->label() }}
                                        </option>
                                    @endforeach
                                </x-ui.select>
                                @error('gender') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Lahir</label>
                                <x-ui.input type="date" name="birth_date" value="{{ old('birth_date') }}" />
                                @error('birth_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Kontak & Alamat -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Kontak & Alamat</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP <span class="text-red-500">*</span></label>
                                <x-ui.input type="text" name="phone" value="{{ old('phone') }}" required placeholder="08..." />
                                @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                                <x-ui.input type="email" name="email" value="{{ old('email') }}" />
                                @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alamat Asal</label>
                                <x-ui.textarea name="address" rows="3">{{ old('address') }}</x-ui.textarea>
                                @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <!-- Dokumen -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Dokumen Lampiran</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto KTP (Max 2MB)</label>
                                <input type="file" name="ktp_photo" accept="image/jpeg,image/png,image/webp"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('ktp_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Foto Profil Penghuni (Max 2MB)</label>
                                <input type="file" name="tenant_photo" accept="image/jpeg,image/png,image/webp"
                                       class="w-full border rounded px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                @error('tenant_photo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Kontak Darurat -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 border-b pb-2 mb-4">Kontak Darurat</h3>
                            
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Kontak Darurat</label>
                                <x-ui.input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" />
                                @error('emergency_contact_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">No. HP Darurat</label>
                                <x-ui.input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="08..." />
                                @error('emergency_contact_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex gap-3">
                        <x-ui.button type="submit">
                            Simpan Data
                        </x-ui.button>
                        <x-ui.button variant="secondary" href="{{ route('tenants.index') }}">
                            Batal
                        </x-ui.button>
                    </div>
                </form>

            </x-ui.card>
        </div>
    </div>
</x-app-layout>
