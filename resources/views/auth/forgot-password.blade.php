<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Lupa Password?</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ __('Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk mereset password ke email tersebut.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Email') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@email.com" />
            @error('email')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" variant="primary" class="w-full">
                {{ __('Kirim Link Reset Password') }}
            </x-ui.button>
        </div>

        <div class="text-center pt-2 border-t border-gray-100 dark:border-gray-800 text-xs text-gray-500 dark:text-gray-400">
            Ingat password Anda? 
            <a href="{{ route('login') }}" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                Kembali ke Login
            </a>
        </div>
    </form>
</x-guest-layout>
