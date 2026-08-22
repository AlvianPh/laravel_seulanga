<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Daftar Akun Baru</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lengkapi informasi berikut untuk membuat akun pengguna</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Nama Lengkap') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
            @error('name')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Email') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@email.com" />
            @error('email')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Password') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="password"
                        type="password"
                        name="password"
                        required autocomplete="new-password"
                        placeholder="Minimal 8 karakter" />
            @error('password')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Konfirmasi Password') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="password_confirmation"
                        type="password"
                        name="password_confirmation" required autocomplete="new-password"
                        placeholder="Ulangi password" />
            @error('password_confirmation')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" variant="primary" class="w-full">
                {{ __('Daftar Sekarang') }}
            </x-ui.button>
        </div>

        <div class="text-center pt-2 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400">
            Sudah memiliki akun? 
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                Masuk di sini
            </a>
        </div>
    </form>
</x-guest-layout>
