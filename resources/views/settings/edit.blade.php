<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Pengaturan Aplikasi') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if (session('success'))
            <x-ui.alert type="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        <x-ui.card>
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Pengaturan Aplikasi Kost</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Konfigurasi identitas kost, kontak pengelola, dan rekening bank resmi</p>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Section: Identitas Kost -->
                    <div class="mb-8">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Identitas Kost</h4>
                        
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="kost_name" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Nama Kost <span class="text-red-500">*</span></label>
                                <x-ui.input type="text" name="kost_name" id="kost_name" value="{{ old('kost_name', $setting->kost_name) }}" required class="text-sm" />
                                @error('kost_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="kost_logo" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Logo Kost (Opsional)</label>
                                @if($setting->kost_logo)
                                    <div class="mb-2 mt-1">
                                        <img src="{{ Storage::url($setting->kost_logo) }}" alt="Logo Kost" class="h-16 w-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-xs">
                                    </div>
                                @endif
                                <input type="file" name="kost_logo" id="kost_logo" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-950/60 dark:file:text-indigo-300">
                                <p class="mt-1 text-[11px] text-gray-400">Format: JPG, PNG, GIF, SVG (Maks 2MB).</p>
                                @error('kost_logo')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="kost_address" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Alamat Lengkap Kost</label>
                                <x-ui.textarea name="kost_address" id="kost_address" rows="3" class="text-sm">{{ old('kost_address', $setting->kost_address) }}</x-ui.textarea>
                                @error('kost_address')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Pengaturan Tagihan -->
                    <div class="mb-8">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Pengaturan Tagihan & Denda</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="default_due_date_day" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Jatuh Tempo Default</label>
                                <x-ui.select name="default_due_date_day" id="default_due_date_day" required class="text-sm">
                                    @for ($i = 1; $i <= 28; $i++)
                                        <option value="{{ $i }}" {{ old('default_due_date_day', $setting->default_due_date_day) == $i ? 'selected' : '' }}>Tanggal {{ $i }} setiap bulan</option>
                                    @endfor
                                </x-ui.select>
                                @error('default_due_date_day')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label for="default_late_fee_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Denda Keterlambatan Default</label>
                                <x-ui.select name="default_late_fee_id" id="default_late_fee_id" class="text-sm">
                                    <option value="">-- Tidak Ada Denda --</option>
                                    @foreach ($feeTypes as $fee)
                                        <option value="{{ $fee->id }}" {{ old('default_late_fee_id', $setting->default_late_fee_id) == $fee->id ? 'selected' : '' }}>
                                            {{ $fee->nama }} 
                                            ({{ $fee->jenis === 'nominal_tetap' ? 'Rp '.number_format($fee->nilai_default, 0, ',', '.') : floatval($fee->nilai_default).'%' }})
                                        </option>
                                    @endforeach
                                </x-ui.select>
                                @error('default_late_fee_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <!-- Section: Rekening Kuitansi -->
                    <div class="mb-8">
                        <h4 class="text-sm font-bold uppercase tracking-wider text-gray-900 dark:text-gray-100 mb-4 border-b border-gray-100 dark:border-gray-700/70 pb-2">Informasi Rekening Pembayaran Default</h4>
                        
                        <div>
                            <label for="default_bank_account_id" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">Rekening Tujuan Default (Untuk Kuitansi / Invoice)</label>
                            <x-ui.select name="default_bank_account_id" id="default_bank_account_id" class="text-sm">
                                <option value="">-- Pilih Rekening Bank --</option>
                                @foreach ($bankAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('default_bank_account_id', $setting->default_bank_account_id) == $account->id ? 'selected' : '' }}>
                                        {{ $account->nama_bank }} - {{ $account->nomor_rekening }} a.n. {{ $account->nama_pemilik_rekening }}
                                    </option>
                                @endforeach
                            </x-ui.select>
                            <p class="mt-1.5 text-[11px] text-gray-500 dark:text-gray-400">Rekening ini akan dicetak otomatis di kuitansi pembayaran untuk instruksi transfer penghuni.</p>
                            @error('default_bank_account_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-gray-100 dark:border-gray-700/60">
                        <x-ui.button type="submit" variant="primary" size="md">
                            Simpan Perubahan
                        </x-ui.button>
                    </div>
                </form>
        </x-ui.card>
    </div>
</x-app-layout>
