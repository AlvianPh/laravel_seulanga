<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User: ') . $user->name }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Profil Pengguna</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui nama, alamat email, hak akses (role), atau atur ulang kata sandi</p>
            </div>

            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <x-ui.input type="text" name="name" value="{{ old('name', $user->name) }}" required class="text-sm" />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                    <x-ui.input type="email" name="email" value="{{ old('email', $user->email) }}" required class="text-sm" />
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Hak Akses / Peran (Role) <span class="text-red-500">*</span></label>
                    <x-ui.select name="role" required class="text-sm">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}"
                                {{ old('role', $user->role->value) === $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="pt-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                        Password Baru <span class="text-gray-400 font-normal normal-case">(kosongkan jika tidak ingin mengubah)</span>
                    </label>
                    <x-ui.input type="password" name="password" class="text-sm" placeholder="Biarkan kosong jika tidak diubah" />
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Password Baru</label>
                    <x-ui.input type="password" name="password_confirmation" class="text-sm" placeholder="Ulangi password baru" />
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Perubahan
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('users.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>

        </x-ui.card>
    </div>
</x-app-layout>
