<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Penghuni: ') . $tenant->name }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>

            @if (session('success'))
                <x-ui.alert type="success">
                    {{ session('success') }}
                </x-ui.alert>
            @endif

            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Data Penghuni: {{ $tenant->name }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui data kontak, NIK, alamat asal, atau ganti dokumen identitas</p>
            </div>

            <form method="POST" action="{{ route('tenants.update', $tenant) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Info Dasar -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Informasi Pribadi</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="name" value="{{ old('name', $tenant->name) }}" required class="text-sm" />
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">NIK (16 Digit) <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="nik" value="{{ old('nik', $tenant->nik) }}" required minlength="16" maxlength="16" pattern="\d{16}"
                                   class="font-mono text-sm" />
                            <p class="text-[11px] text-gray-400 mt-1">Hanya angka, tepat 16 digit.</p>
                            @error('nik') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                            <x-ui.select name="gender" required class="text-sm">
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender->value }}" {{ old('gender', $tenant->gender->value) === $gender->value ? 'selected' : '' }}>
                                        {{ $gender->label() }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                            @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Lahir</label>
                            <x-ui.input type="date" name="birth_date" value="{{ old('birth_date', $tenant->birth_date ? $tenant->birth_date->format('Y-m-d') : '') }}" class="text-sm" />
                            @error('birth_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kontak & Alamat -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Kontak & Alamat</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">No. HP <span class="text-red-500">*</span></label>
                            <x-ui.input type="text" name="phone" value="{{ old('phone', $tenant->phone) }}" required class="text-sm" />
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                            <x-ui.input type="email" name="email" value="{{ old('email', $tenant->email) }}" class="text-sm" />
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Alamat Asal</label>
                            <x-ui.textarea name="address" rows="3" class="text-sm">{{ old('address', $tenant->address) }}</x-ui.textarea>
                            @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 pt-6 border-t border-gray-100 dark:border-gray-700/70">
                    <!-- Dokumen Baru -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Ganti Dokumen Lampiran</h4>
                        
                        <div x-data="{
                            previewUrl: null,
                            handleFileChange(event) {
                                const file = event.target.files[0];
                                if (file && file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { this.previewUrl = e.target.result; };
                                    reader.readAsDataURL(file);
                                } else {
                                    this.previewUrl = null;
                                }
                            }
                        }" class="p-3.5 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Upload Foto KTP Baru (Max 2MB)</label>
                            
                            <div x-show="previewUrl" x-cloak class="mb-2.5">
                                <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 block mb-1">Preview KTP Baru:</span>
                                <img :src="previewUrl" alt="Preview KTP Baru" class="h-24 w-auto max-w-[160px] object-cover rounded-xl border-2 border-indigo-500/80 shadow-xs">
                            </div>

                            <input type="file" name="ktp_photo" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/webp"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 cursor-pointer">
                            @error('ktp_photo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div x-data="{
                            previewUrl: null,
                            handleFileChange(event) {
                                const file = event.target.files[0];
                                if (file && file.type.startsWith('image/')) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { this.previewUrl = e.target.result; };
                                    reader.readAsDataURL(file);
                                } else {
                                    this.previewUrl = null;
                                }
                            }
                        }" class="p-3.5 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 hover:border-indigo-400 dark:hover:border-indigo-500 transition-colors">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Upload Foto Profil Baru (Max 2MB)</label>
                            
                            <div x-show="previewUrl" x-cloak class="mb-2.5">
                                <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 block mb-1">Preview Profil Baru:</span>
                                <img :src="previewUrl" alt="Preview Profil Baru" class="h-24 w-24 object-cover rounded-xl border-2 border-indigo-500/80 shadow-xs">
                            </div>

                            <input type="file" name="tenant_photo" @change="handleFileChange($event)" accept="image/jpeg,image/png,image/webp"
                                   class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300 cursor-pointer">
                            @error('tenant_photo') <p class="text-rose-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <!-- Kontak Darurat -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 border-b border-gray-100 dark:border-gray-700/70 pb-2">Kontak Darurat</h4>
                        
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Kontak Darurat</label>
                            <x-ui.input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $tenant->emergency_contact_name) }}" class="text-sm" />
                            @error('emergency_contact_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">No. HP Darurat</label>
                            <x-ui.input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $tenant->emergency_contact_phone) }}" class="text-sm" />
                            @error('emergency_contact_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Perubahan
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('tenants.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>

            <!-- Kelola File Eksisting -->
            <div class="mt-8 border-t border-gray-100 dark:border-gray-700/70 pt-6">
                <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4">Dokumen Eksisting</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Foto KTP -->
                    <div class="border border-gray-200/80 dark:border-gray-700/70 rounded-2xl p-4 bg-gray-50/50 dark:bg-gray-800/40">
                        <h5 class="text-xs font-bold uppercase tracking-wider mb-2 text-gray-700 dark:text-gray-300">Foto KTP</h5>
                        @if($tenant->ktp_photo_path)
                            <img src="{{ Storage::url($tenant->ktp_photo_path) }}" class="w-full h-44 object-cover rounded-xl mb-3 shadow-xs" alt="Foto KTP">
                            <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.ktp.destroy', $tenant) }}', name: 'foto KTP ini' })">Hapus KTP</x-ui.button>
                        @else
                            <p class="text-xs text-gray-400 italic py-6 text-center">Belum ada foto KTP.</p>
                        @endif
                    </div>

                    <!-- Foto Profil -->
                    <div class="border border-gray-200/80 dark:border-gray-700/70 rounded-2xl p-4 bg-gray-50/50 dark:bg-gray-800/40">
                        <h5 class="text-xs font-bold uppercase tracking-wider mb-2 text-gray-700 dark:text-gray-300">Foto Profil</h5>
                        @if($tenant->tenant_photo_path)
                            <img src="{{ Storage::url($tenant->tenant_photo_path) }}" class="w-full h-44 object-cover rounded-xl mb-3 shadow-xs" alt="Foto Profil">
                            <x-ui.button size="sm" variant="danger" type="button" @click.prevent="$dispatch('open-delete-modal', { url: '{{ route('tenants.photo.destroy', $tenant) }}', name: 'foto profil ini' })">Hapus Profil</x-ui.button>
                        @else
                            <p class="text-xs text-gray-400 italic py-6 text-center">Belum ada foto profil.</p>
                        @endif
                    </div>
                </div>
            </div>

        </x-ui.card>
    </div>
</x-app-layout>
