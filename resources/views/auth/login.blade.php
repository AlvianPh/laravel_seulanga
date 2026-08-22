<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Selamat Datang</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Masukkan kredensial akun Anda untuk mengakses sistem</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Email') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@email.com" />
            @error('email')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300">
                    {{ __('Password') }} <span class="text-rose-500">*</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline" href="{{ route('password.request') }}">
                        {{ __('Lupa password?') }}
                    </a>
                @endif
            </div>

            <x-ui.input id="password"
                        type="password"
                        name="password"
                        required autocomplete="current-password"
                        placeholder="••••••••" />
            @error('password')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-indigo-600 shadow-xs focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-xs font-medium text-gray-600 dark:text-gray-400">{{ __('Ingat saya') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" variant="primary" class="w-full">
                {{ __('Masuk') }}
            </x-ui.button>
        </div>

        @if (Route::has('register'))
            <div class="text-center pt-2 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Daftar di sini
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
