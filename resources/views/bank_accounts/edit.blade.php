<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Rekening') }}
        </h2>
    </x-slot>

    <div class="max-w-xl mx-auto space-y-6">
        <x-ui.card>
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Edit Rekening Bank</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Perbarui rincian rekening tujuan transfer</p>
            </div>

            <form action="{{ route('bank_accounts.update', $bankAccount) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="nama_bank" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Bank <span class="text-red-500">*</span></label>
                    <x-ui.input type="text" name="nama_bank" id="nama_bank" value="{{ old('nama_bank', $bankAccount->nama_bank) }}" required class="text-sm" />
                    @error('nama_bank')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="nomor_rekening" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nomor Rekening <span class="text-red-500">*</span></label>
                    <x-ui.input type="text" name="nomor_rekening" id="nomor_rekening" value="{{ old('nomor_rekening', $bankAccount->nomor_rekening) }}" required class="text-sm" />
                    @error('nomor_rekening')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-4">
                    <label for="nama_pemilik_rekening" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Pemilik Rekening <span class="text-red-500">*</span></label>
                    <x-ui.input type="text" name="nama_pemilik_rekening" id="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $bankAccount->nama_pemilik_rekening) }}" required class="text-sm" />
                    @error('nama_pemilik_rekening')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6 flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-xs focus:ring-indigo-500" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="ml-2 block text-xs font-medium text-gray-700 dark:text-gray-300">Rekening Aktif</label>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100 dark:border-gray-700/60">
                    <x-ui.button type="submit" variant="primary">Simpan Perubahan</x-ui.button>
                    <x-ui.button variant="secondary" href="{{ route('bank_accounts.index') }}">Batal</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
