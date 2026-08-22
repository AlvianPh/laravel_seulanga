<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Tambah User Baru') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Pengguna Baru</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Buat akun akses sistem baru untuk staf admin atau owner kost</p>
            </div>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <x-ui.input type="text" name="name" value="{{ old('name') }}" required class="text-sm" placeholder="Contoh: Sarah Jenkins" />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Alamat Email <span class="text-red-500">*</span></label>
                    <x-ui.input type="email" name="email" value="{{ old('email') }}" required class="text-sm" placeholder="admin@kostseulanga.com" />
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Hak Akses / Peran (Role) <span class="text-red-500">*</span></label>
                    <x-ui.select name="role" required class="text-sm">
                        @foreach ($roles as $role)
                            <option value="{{ $role->value }}" {{ old('role') === $role->value ? 'selected' : '' }}>
                                {{ $role->label() }}
                            </option>
                        @endforeach
                    </x-ui.select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Kata Sandi (Password) <span class="text-red-500">*</span></label>
                    <x-ui.input type="password" name="password" required class="text-sm" placeholder="Minimal 8 karakter" />
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Konfirmasi Kata Sandi <span class="text-red-500">*</span></label>
                    <x-ui.input type="password" name="password_confirmation" required class="text-sm" placeholder="Ulangi kata sandi" />
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Pengguna
                    </x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('users.index') }}">
                        Batal
                    </x-ui.button>
                </div>
            </form>

        </x-ui.card>
    </div>
</x-app-layout>
