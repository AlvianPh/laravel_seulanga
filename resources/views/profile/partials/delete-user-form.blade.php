<section class="space-y-6">
    <header>
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
            {{ __('Hapus Akun Pengguna') }}
        </h3>

        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
            {{ __('Setelah akun Anda dihapus, seluruh sumber daya dan data terkait akan dihapus secara permanen. Sebelum menghapus akun, pastikan Anda telah menyimpan data penting yang ingin Anda pertahankan.') }}
        </p>
    </header>

    <x-ui.button
        type="button"
        variant="danger"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Hapus Akun') }}</x-ui.button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8">
            @csrf
            @method('delete')

            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                {{ __('Konfirmasi Hapus Akun') }}
            </h3>

            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda secara permanen? Masukkan password akun Anda untuk mengonfirmasi tindakan ini.') }}
            </p>

            <div class="mt-5">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5 sr-only">
                    {{ __('Password') }}
                </label>

                <x-ui.input
                    id="password"
                    name="password"
                    type="password"
                    class="w-full"
                    placeholder="{{ __('Masukkan Password Anda') }}"
                />

                @if ($errors->userDeletion->has('password'))
                    <p class="text-rose-500 text-xs mt-1.5">{{ $errors->userDeletion->first('password') }}</p>
                @endif
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-ui.button>

                <x-ui.button type="submit" variant="danger">
                    {{ __('Ya, Hapus Akun') }}
                </x-ui.button>
            </div>
        </form>
    </x-modal>
</section>
