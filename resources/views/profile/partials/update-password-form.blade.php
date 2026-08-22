<section>
    <header>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ __('Perbarui Password') }}
        </h3>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ __('Pastikan akun Anda menggunakan password yang panjang dan acak untuk menjaga keamanan.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Password Saat Ini') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" placeholder="••••••••" />
            @if ($errors->updatePassword->has('current_password'))
                <p class="text-rose-500 text-xs mt-1">{{ $errors->updatePassword->first('current_password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Password Baru') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="update_password_password" name="password" type="password" autocomplete="new-password" placeholder="Minimal 8 karakter" />
            @if ($errors->updatePassword->has('password'))
                <p class="text-rose-500 text-xs mt-1">{{ $errors->updatePassword->first('password') }}</p>
            @endif
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                {{ __('Konfirmasi Password Baru') }} <span class="text-rose-500">*</span>
            </label>
            <x-ui.input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" placeholder="Ulangi password baru" />
            @if ($errors->updatePassword->has('password_confirmation'))
                <p class="text-rose-500 text-xs mt-1">{{ $errors->updatePassword->first('password_confirmation') }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-ui.button type="submit" variant="primary">{{ __('Simpan Password') }}</x-ui.button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Password berhasil diperbarui.') }}
                </p>
            @endif
        </div>
    </form>
</section>
