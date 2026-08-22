<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Atur Ulang Password</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Buat password baru yang kuat untuk akun Anda</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Email') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            @error('email')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Password Baru') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            @error('password')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Konfirmasi Password Baru') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="password_confirmation"
                        type="password"
                        name="password_confirmation" required autocomplete="new-password"
                        placeholder="Ulangi password baru" />
            @error('password_confirmation')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" variant="primary" class="w-full">
                {{ __('Simpan Password Baru') }}
            </x-ui.button>
        </div>
    </form>
</x-guest-layout>
