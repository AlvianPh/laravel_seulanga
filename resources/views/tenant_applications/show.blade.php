<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('tenant-applications.index') }}" class="p-2 rounded-xl bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-slate-800 dark:text-slate-200 leading-tight">
                        Review Pengajuan #{{ $tenantApplication->id }}
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                        Pemohon: {{ $tenantApplication->tenant?->name }} • Kamar {{ $tenantApplication->room?->room_number }}
                    </p>
                </div>
            </div>
            <div>
                <x-ui.badge :variant="$tenantApplication->status->badgeVariant()">
                    {{ $tenantApplication->status->label() }}
                </x-ui.badge>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6 max-w-5xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left Column: Applicant & Room Info (2 cols) -->
            <div class="md:col-span-2 space-y-6">
                <!-- Applicant Info -->
                <x-ui.card>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Informasi Calon Penghuni
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400">Nama Lengkap</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $tenantApplication->tenant?->name }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">NIK (KTP)</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $tenantApplication->tenant?->nik }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">Nomor Telepon / WhatsApp</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $tenantApplication->tenant?->phone }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">Alamat Email</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $tenantApplication->tenant?->email ?: '-' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">Kontak Darurat</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $tenantApplication->tenant?->emergency_contact_name ?: '-' }}
                                @if ($tenantApplication->tenant?->emergency_contact_phone)
                                    ({{ $tenantApplication->tenant->emergency_contact_phone }})
                                @endif
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">Alamat Asal</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $tenantApplication->tenant?->address ?: '-' }}
                            </p>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Selected Room Info -->
                <x-ui.card>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Kamar yang Dipilih
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400">Nomor & Lantai</span>
                            <p class="font-bold text-slate-800 dark:text-slate-200 mt-0.5">
                                Kamar {{ $tenantApplication->room?->room_number }} (Lt. {{ $tenantApplication->room?->floor }})
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">Tipe Kamar</span>
                            <p class="font-semibold text-slate-800 dark:text-slate-200 mt-0.5">
                                {{ $tenantApplication->room?->roomType?->name ?? 'Standard' }}
                            </p>
                        </div>
                        <div>
                            <span class="text-xs text-slate-400">Tarif Sewa</span>
                            <p class="font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">
                                Rp {{ number_format($tenantApplication->room?->monthly_price ?? 0, 0, ',', '.') }}/bln
                            </p>
                        </div>
                    </div>

                    <!-- Facilities -->
                    @if ($tenantApplication->room?->facilities->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-xs text-slate-400 block mb-2">Fasilitas Kamar:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($tenantApplication->room->facilities as $facility)
                                    <span class="text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-medium">
                                        {{ $facility->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </x-ui.card>

                <!-- Application Notes -->
                <x-ui.card>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">Catatan dari Pemohon</h3>
                    <p class="text-sm text-slate-700 dark:text-slate-300">
                        {{ $tenantApplication->application_notes ?: 'Tidak ada catatan khusus yang dilampirkan.' }}
                    </p>
                </x-ui.card>
            </div>

            <!-- Right Column: Review Action Box (1 col) -->
            <div class="space-y-6">
                <x-ui.card>
                    <h3 class="text-base font-bold text-slate-900 dark:text-white mb-3">
                        Tindakan Review
                    </h3>

                    @if ($tenantApplication->status === \App\Enums\StatusApplication::Pending)
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-5 leading-relaxed">
                            Silakan lakukan verifikasi data calon penghuni dan tentukan persetujuan pengajuan.
                        </p>

                        <!-- Form Approve -->
                        <form method="POST" action="{{ route('tenant-applications.review', $tenantApplication) }}" class="mb-4">
                            @csrf
                            <input type="hidden" name="action" value="approve">
                            <x-ui.button type="submit" variant="primary" class="w-full justify-center" onclick="return confirm('Setujui pengajuan ini? Calon penghuni akan menerima konfirmasi.');">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Setujui Pengajuan
                            </x-ui.button>
                        </form>

                        <!-- Form Reject -->
                        <div x-data="{ openReject: false }">
                            <button type="button"
                                    @click="openReject = !openReject"
                                    class="w-full py-2.5 px-4 rounded-xl text-sm font-semibold bg-red-50 hover:bg-red-100 text-red-700 dark:bg-red-950/40 dark:hover:bg-red-950/60 dark:text-red-300 border border-red-200 dark:border-red-800 transition-colors flex items-center justify-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Tolak Pengajuan
                            </button>

                            <div x-show="openReject" x-cloak class="mt-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                                <form method="POST" action="{{ route('tenant-applications.review', $tenantApplication) }}" class="space-y-3">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">

                                    <div>
                                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">
                                            Alasan Penolakan <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="rejection_reason"
                                                  rows="3"
                                                  required
                                                  placeholder="Contoh: Kamar sudah terisi penyewa lain / Dokumen NIK tidak valid"
                                                  class="w-full text-xs rounded-lg border-slate-300 dark:border-slate-700 dark:bg-slate-900 text-slate-900 dark:text-white focus:ring-red-500 focus:border-red-500"></textarea>
                                    </div>

                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" @click="openReject = false" class="px-3 py-1.5 text-xs text-slate-500 hover:text-slate-700">Batal</button>
                                        <button type="submit" class="px-3 py-1.5 text-xs font-semibold rounded-lg bg-red-600 text-white hover:bg-red-700">
                                            Konfirmasi Tolak
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <!-- Already Reviewed Status -->
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-xs space-y-2">
                            <div>
                                <span class="text-slate-400">Ditinjau Oleh:</span>
                                <p class="font-bold text-slate-800 dark:text-slate-200">
                                    {{ $tenantApplication->reviewer?->name ?: '-' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-slate-400">Waktu Review:</span>
                                <p class="font-semibold text-slate-800 dark:text-slate-200">
                                    {{ $tenantApplication->reviewed_at?->translatedFormat('d F Y, H:i') ?: '-' }}
                                </p>
                            </div>
                            @if ($tenantApplication->rejection_reason)
                                <div>
                                    <span class="text-red-500 font-semibold">Alasan Penolakan:</span>
                                    <p class="text-slate-700 dark:text-slate-300 mt-0.5">
                                        {{ $tenantApplication->rejection_reason }}
                                    </p>
                                </div>
                            @endif
                        </div>

                        @if ($tenantApplication->status === \App\Enums\StatusApplication::Approved)
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                                <a href="{{ route('contracts.create', ['application_id' => $tenantApplication->id, 'tenant_id' => $tenantApplication->tenant_id, 'room_id' => $tenantApplication->room_id]) }}"
                                   class="w-full flex items-center justify-center gap-1.5 py-2.5 px-4 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                                    Buat Draft Kontrak Sewa &rarr;
                                </a>
                            </div>
                        @endif
                    @endif
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
