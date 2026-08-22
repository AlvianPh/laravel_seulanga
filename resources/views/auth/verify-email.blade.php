<x-guest-layout>
    <div class="mb-6">
        <h2 class="text-xl font-extrabold text-gray-900 dark:text-white tracking-tight">Verifikasi Alamat Email</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
            {{ __('Terima kasih telah mendaftar! Sebelum memulai, silakan verifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email Anda.') }}
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-xs text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/40 p-3 rounded-xl border border-emerald-200 dark:border-emerald-800">
            {{ __('Tautan verifikasi baru telah dikirimkan ke alamat email yang Anda daftarkan.') }}
        </div>
    @endif

    <div class="space-y-4 pt-2">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-ui.button type="submit" variant="primary" class="w-full">
                {{ __('Kirim Ulang Email Verifikasi') }}
            </x-ui.button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf
            <button type="submit" class="text-xs font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white underline">
                {{ __('Keluar / Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
