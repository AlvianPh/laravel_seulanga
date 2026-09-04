<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Page Header -->
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white">Profil & Data Penghuni</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                Kelola informasi kontak dan pengaturan keamanan akun Anda.
            </p>
        </div>

        <!-- Profil Form Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Informasi Pribadi & Kontak</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                Pastikan nomor telepon dan kontak darurat selalu aktif untuk keperluan operasional kost.
            </p>

            <form method="POST" action="{{ route('portal.profile.update') }}" class="space-y-5">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Nama Lengkap -->
                    <div>
                        <x-ui.input
                            name="name"
                            label="Nama Lengkap"
                            :value="old('name', $user->name)"
                            required
                        />
                    </div>

                    <!-- Email -->
                    <div>
                        <x-ui.input
                            name="email"
                            label="Alamat Email"
                            type="email"
                            :value="old('email', $user->email)"
                            required
                        />
                    </div>

                    <!-- NIK (Read Only) -->
                    <div>
                        <x-ui.input
                            name="nik"
                            label="NIK KTP (Terkunci)"
                            :value="$tenant?->nik ?? 'Belum ada data NIK'"
                            disabled
                        />
                        <p class="mt-1 text-[11px] text-slate-400">Perubahan NIK memerlukan verifikasi identitas fisik ke Admin.</p>
                    </div>

                    <!-- Nomor HP / WhatsApp -->
                    <div>
                        <x-ui.input
                            name="phone"
                            label="Nomor HP / WhatsApp"
                            :value="old('phone', $tenant?->phone)"
                            placeholder="081234567890"
                        />
                    </div>

                    <!-- Kontak Darurat: Nama -->
                    <div>
                        <x-ui.input
                            name="emergency_contact_name"
                            label="Nama Kontak Darurat"
                            :value="old('emergency_contact_name', $tenant?->emergency_contact_name)"
                            placeholder="Orang tua / Wali / Kerabat"
                        />
                    </div>

                    <!-- Kontak Darurat: Nomor HP -->
                    <div>
                        <x-ui.input
                            name="emergency_contact_phone"
                            label="Nomor HP Kontak Darurat"
                            :value="old('emergency_contact_phone', $tenant?->emergency_contact_phone)"
                            placeholder="081234567890"
                        />
                    </div>

                    <!-- Alamat Asal -->
                    <div class="sm:col-span-2">
                        <x-ui.textarea
                            name="address"
                            label="Alamat Asal KTP"
                            rows="3"
                            :value="old('address', $tenant?->address)"
                            placeholder="Alamat lengkap sesuai KTP"
                        />
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-slate-800">
                    <x-ui.button type="submit" variant="primary">
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            </form>
        </div>

        <!-- Ubah Password Card -->
        <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Keamanan Sandi</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">
                Gunakan kata sandi yang kuat dan unik untuk menjaga keamanan akun Anda.
            </p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5 max-w-lg">
                @csrf
                @method('PUT')

                <div>
                    <x-ui.input
                        name="current_password"
                        label="Kata Sandi Saat Ini"
                        type="password"
                        required
                    />
                </div>

                <div>
                    <x-ui.input
                        name="password"
                        label="Kata Sandi Baru"
                        type="password"
                        required
                    />
                </div>

                <div>
                    <x-ui.input
                        name="password_confirmation"
                        label="Konfirmasi Kata Sandi Baru"
                        type="password"
                        required
                    />
                </div>

                <div class="pt-2">
                    <x-ui.button type="submit" variant="secondary">
                        Perbarui Kata Sandi
                    </x-ui.button>
                </div>
            </form>
        </div>
    </div>
</x-portal-layout>
