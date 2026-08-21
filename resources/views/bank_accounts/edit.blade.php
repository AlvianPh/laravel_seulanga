<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Rekening') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-ui.card>
                    <form action="{{ route('bank_accounts.update', $bankAccount) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="nama_bank" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Bank</label>
                            <x-ui.input type="text" name="nama_bank" id="nama_bank" value="{{ old('nama_bank', $bankAccount->nama_bank) }}" required />
                            @error('nama_bank')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="nomor_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Rekening</label>
                            <x-ui.input type="text" name="nomor_rekening" id="nomor_rekening" value="{{ old('nomor_rekening', $bankAccount->nomor_rekening) }}" required />
                            @error('nomor_rekening')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="nama_pemilik_rekening" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Pemilik Rekening</label>
                            <x-ui.input type="text" name="nama_pemilik_rekening" id="nama_pemilik_rekening" value="{{ old('nama_pemilik_rekening', $bankAccount->nama_pemilik_rekening) }}" required />
                            @error('nama_pemilik_rekening')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div class="mb-6 flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ old('is_active', $bankAccount->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm text-gray-900 dark:text-gray-300">Aktif</label>
                        </div>

                        <div class="flex items-center justify-end gap-3">
                            <x-ui.button variant="secondary" href="{{ route('bank_accounts.index') }}">Batal</x-ui.button>
                            <x-ui.button type="submit">Update</x-ui.button>
                        </div>
                    </form>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
