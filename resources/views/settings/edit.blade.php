<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Aplikasi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Section: Identitas Kost -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Identitas Kost</h3>
                            
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label for="kost_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kost</label>
                                    <input type="text" name="kost_name" id="kost_name" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" value="{{ old('kost_name', $setting->kost_name) }}" required>
                                    @error('kost_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="kost_logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logo Kost (Opsional)</label>
                                    @if($setting->kost_logo)
                                        <div class="mb-2 mt-2">
                                            <img src="{{ Storage::url($setting->kost_logo) }}" alt="Logo Kost" class="h-16 w-auto rounded border border-gray-200 dark:border-gray-700 shadow-sm">
                                        </div>
                                    @endif
                                    <input type="file" name="kost_logo" id="kost_logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/40 dark:file:text-indigo-300">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG, GIF, SVG (Maks 2MB).</p>
                                    @error('kost_logo')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="kost_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alamat Lengkap</label>
                                    <textarea name="kost_address" id="kost_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('kost_address', $setting->kost_address) }}</textarea>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Digunakan sebagai kop di kuitansi dan laporan.</p>
                                    @error('kost_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section: Pengaturan Tagihan -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Pengaturan Tagihan</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="default_due_date_day" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Jatuh Tempo Default</label>
                                    <select name="default_due_date_day" id="default_due_date_day" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                        @for ($i = 1; $i <= 28; $i++)
                                            <option value="{{ $i }}" {{ old('default_due_date_day', $setting->default_due_date_day) == $i ? 'selected' : '' }}>Tanggal {{ $i }}</option>
                                        @endfor
                                    </select>
                                    @error('default_due_date_day')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label for="default_late_fee_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Denda Keterlambatan Default</label>
                                    <select name="default_late_fee_id" id="default_late_fee_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        <option value="">-- Tidak Ada Denda --</option>
                                        @foreach ($feeTypes as $fee)
                                            <option value="{{ $fee->id }}" {{ old('default_late_fee_id', $setting->default_late_fee_id) == $fee->id ? 'selected' : '' }}>
                                                {{ $fee->nama }} 
                                                ({{ $fee->jenis === 'nominal_tetap' ? 'Rp '.number_format($fee->nilai_default, 0, ',', '.') : floatval($fee->nilai_default).'%' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('default_late_fee_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <!-- Section: Rekening Kuitansi -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-200 dark:border-gray-700 pb-2">Informasi Pembayaran</h3>
                            
                            <div>
                                <label for="default_bank_account_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rekening Tujuan Default (Untuk Kuitansi)</label>
                                <select name="default_bank_account_id" id="default_bank_account_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">-- Pilih Rekening --</option>
                                    @foreach ($bankAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('default_bank_account_id', $setting->default_bank_account_id) == $account->id ? 'selected' : '' }}>
                                            {{ $account->nama_bank }} - {{ $account->nomor_rekening }} a.n. {{ $account->nama_pemilik_rekening }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Rekening ini akan dicetak di kuitansi pembayaran untuk instruksi transfer.</p>
                                @error('default_bank_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors font-medium">Simpan Pengaturan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
