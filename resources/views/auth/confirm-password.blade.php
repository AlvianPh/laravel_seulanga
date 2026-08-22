<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Konfirmasi Password</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ __('Ini adalah area yang aman. Harap konfirmasikan password Anda sebelum melanjutkan.') }}
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-4">
        @csrf

        <!-- Password -->
        <div>
            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Password') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="password"
                        type="password"
                        name="password"
                        required autocomplete="current-password"
                        placeholder="••••••••" />
            @error('password')
                <p class="text-rose-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="pt-2">
            <x-ui.button type="submit" variant="primary" class="w-full">
                {{ __('Konfirmasi') }}
            </x-ui.button>
        </div>
    </form>
</x-guest-layout>
