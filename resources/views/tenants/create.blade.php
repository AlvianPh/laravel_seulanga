<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah Penghuni Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Data Penghuni Baru</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Daftarkan identitas lengkap penyewa, kontak, dokumen KTP, dan kontak darurat</p>
            </div>

            <form method="POST" action="{{ route('tenants.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Info Pribadi -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Informasi Pribadi</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="name" value="{{ old('name') }}" required class="text-sm" placeholder="Nama sesuai KTP" />
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="nik" value="{{ old('nik') }}" required minlength="16" maxlength="16" pattern="\d{16}"
                                   class="font-mono text-sm" placeholder="Contoh: 3201..." />
                            <p class="text-[11px] text-gray-400 mt-1">Hanya angka, tepat 16 digit NIK KTP.</p>
                            @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <x-ui.select name="gender" required class="text-sm">
                                <option value="" disabled selected>Pilih Jenis Kelamin</option>
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender->value }}" {{ old('gender') === $gender->value ? 'selected' : '' }}>
                                        {{ $gender->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Lahir</label>
                            <x-ui.input type="date" name="birth_date" value="{{ old('birth_date') }}" class="text-sm" />
                            @error('birth_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kontak & Alamat -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Kontak & Alamat Asal</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">No. WhatsApp / HP <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Contoh: 08123456789" class="text-sm" />
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                            <x-ui.input type="email" name="email" value="{{ old('email') }}" class="text-sm" placeholder="email@domain.com" />
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Alamat Asal (Sesuai KTP)</label>
                            <x-ui.textarea name="address" rows="3" class="text-sm" placeholder="Alamat domisili asal penghuni">{{ old('address') }}</x-ui.textarea>
                            @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700/70">
                    <!-- Dokumen -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Dokumen Lampiran</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Foto KTP (Maks 2MB)</label>
                            <input type="file" name="ktp_photo" accept="image/jpeg,image/png,image/webp"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300">
                            @error('ktp_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Foto Profil Penghuni (Maks 2MB)</label>
                            <input type="file" name="tenant_photo" accept="image/jpeg,image/png,image/webp"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300">
                            @error('tenant_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kontak Darurat -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Kontak Darurat (Keluarga / Kerabat)</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Kontak Darurat</label>
                            <x-ui.input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="text-sm" placeholder="Contoh: Ayah / Ibu / Saudara" />
                            @error('emergency_contact_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">No. HP Kontak Darurat</label>
                            <x-ui.input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" placeholder="Contoh: 08123456789" class="text-sm" />
                            @error('emergency_contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Data Penghuni
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('tenants.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
