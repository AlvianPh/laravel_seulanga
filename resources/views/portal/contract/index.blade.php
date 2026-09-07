<x-portal-layout>
    <div class="space-y-6 max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Kontrak Sewa & Onboarding
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Pelacakan status penerbitan kontrak, persetujuan tata tertib sewa, dan pembayaran awal.
                </p>
            </div>
            @if ($contract)
                <div>
                    <x-ui.badge :variant="$contract->status->badgeVariant()">
                        {{ $contract->status->label() }}
                    </x-ui.badge>
                </div>
            @endif
        </div>

        @if (! $contract)
            <!-- Empty State -->
            <div class="p-8 sm:p-12 text-center rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
                    Belum Ada Kontrak Sewa
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                    Anda belum memiliki draft atau kontrak sewa aktif. Silakan cari kamar yang tersedia dan kirim pengajuan sewa terlebih dahulu.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('portal.rooms.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Cari Kamar Kost
                    </a>
                    <a href="{{ route('portal.applications.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors">
                        Status Pengajuan Saya
                    </a>
                </div>
            </div>
        @else
            <!-- Onboarding Stepper Progress -->
            <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-4">
                    Alur Proses Onboarding Penghuni
                </h3>
                
                @php
                    $isDraft = $contract->isDraft();
                    $isAgreementAccepted = $contract->isAgreementAccepted();
                    $initialInvoice = $contract->initialInvoice();
                    $isPaymentVerified = $contract->hasVerifiedInitialPayment();
                    $isActive = $contract->isActive();
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <!-- Step 1: Draft Kontrak -->
                    <div class="p-3.5 rounded-xl border {{ $contract ? 'bg-emerald-50/60 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/80 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-400' }}">
                        <div class="flex items-center gap-2 font-bold text-xs">
                            <div class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</div>
                            <span>1. Draft Kontrak</span>
                        </div>
                        <p class="text-[11px] mt-1 text-slate-600 dark:text-slate-400">Kontrak telah diterbitkan</p>
                    </div>

                    <!-- Step 2: Tata Tertib -->
                    <div class="p-3.5 rounded-xl border {{ $isAgreementAccepted ? 'bg-emerald-50/60 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/80 text-emerald-900 dark:text-emerald-200' : 'bg-amber-50/60 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800/80 text-amber-900 dark:text-amber-200' }}">
                        <div class="flex items-center gap-2 font-bold text-xs">
                            @if ($isAgreementAccepted)
                                <div class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</div>
                            @else
                                <div class="w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center text-[10px]">!</div>
                            @endif
                            <span>2. Tata Tertib</span>
                        </div>
                        <p class="text-[11px] mt-1 {{ $isAgreementAccepted ? 'text-slate-600 dark:text-slate-400' : 'text-amber-700 dark:text-amber-300 font-semibold' }}">
                            {{ $isAgreementAccepted ? 'Telah disetujui' : 'Menunggu persetujuan' }}
                        </p>
                    </div>

                    <!-- Step 3: Pembayaran Awal -->
                    <div class="p-3.5 rounded-xl border {{ $isPaymentVerified ? 'bg-emerald-50/60 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/80 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                        <div class="flex items-center gap-2 font-bold text-xs">
                            @if ($isPaymentVerified)
                                <div class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</div>
                            @else
                                <div class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-[10px]">3</div>
                            @endif
                            <span>3. Pembayaran Awal</span>
                        </div>
                        <p class="text-[11px] mt-1 text-slate-600 dark:text-slate-400">
                            {{ $isPaymentVerified ? 'Pembayaran terverifikasi' : 'Tagihan sewa awal' }}
                        </p>
                    </div>

                    <!-- Step 4: Aktivasi -->
                    <div class="p-3.5 rounded-xl border {{ $isActive ? 'bg-emerald-50/60 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800/80 text-emerald-900 dark:text-emerald-200' : 'bg-slate-50 dark:bg-slate-800/60 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                        <div class="flex items-center gap-2 font-bold text-xs">
                            @if ($isActive)
                                <div class="w-5 h-5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[10px]">✓</div>
                            @else
                                <div class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-700 text-slate-700 dark:text-slate-300 flex items-center justify-center text-[10px]">4</div>
                            @endif
                            <span>4. Kontrak Aktif</span>
                        </div>
                        <p class="text-[11px] mt-1 text-slate-600 dark:text-slate-400">
                            {{ $isActive ? 'Kamar siap ditempati' : 'Verifikasi akhir pengelola' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contract Summary Card -->
            <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Nomor Kontrak</span>
                        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                            #CTR-{{ str_pad($contract->id, 5, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Harga Sewa Bulanan</span>
                        <p class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-0.5">
                            Rp {{ number_format($contract->rent_price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <span class="text-xs text-slate-400">Kamar & Lantai</span>
                        <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                            Kamar {{ $contract->room?->room_number }} (Lt. {{ $contract->room?->floor }})
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <span class="text-xs text-slate-400">Periode Sewa</span>
                        <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                            {{ $contract->start_date->format('d M Y') }} — {{ $contract->end_date->format('d M Y') }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <span class="text-xs text-slate-400">Uang Jaminan / Deposit</span>
                        <p class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                            Rp {{ number_format($contract->deposit_amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60">
                        <span class="text-xs text-slate-400">Status Kontrak</span>
                        <p class="text-sm font-bold mt-1">
                            <x-ui.badge :variant="$contract->status->badgeVariant()">
                                {{ $contract->status->label() }}
                            </x-ui.badge>
                        </p>
                    </div>
                </div>

                @if ($contract->notes)
                    <div class="mt-6 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 text-xs">
                        <span class="font-bold text-slate-500 dark:text-slate-400 block mb-1">Catatan Tambahan:</span>
                        <p class="text-slate-700 dark:text-slate-300">{{ $contract->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Agreement & House Rules Section -->
            <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm" id="agreement-section">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tata Tertib & Kesepakatan Sewa</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Ketentuan resmi sewa hunian {{ $setting->kost_name ?? 'Seulanga Kost' }}</p>
                        </div>
                    </div>
                    @if ($contract->isAgreementAccepted())
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                            ✓ Telah Disetujui
                        </span>
                    @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300 border border-amber-200 dark:border-amber-800">
                            Wajib Disetujui
                        </span>
                    @endif
                </div>

                <!-- House Rules Box -->
                <div class="p-5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-xs text-slate-700 dark:text-slate-300 leading-relaxed space-y-4 max-h-80 overflow-y-auto">
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1">1. Identitas & Penghunian Kamar</h4>
                        <p>Kamar hanya diperuntukkan bagi penghuni yang terdaftar resmi. Tidak diperkenankan memindahtangankan sewa atau menampung orang lain untuk tinggal menetap tanpa izin tertulis dari pengelola.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1">2. Pembayaran Sewa & Tagihan</h4>
                        <p>Pembayaran sewa bulanan wajib diselesaikan paling lambat pada tanggal jatuh tempo setiap bulannya. Keterlambatan pembayaran dapat dikenakan denda sesuai ketentuan yang berlaku.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1">3. Jam Kunjungan & Tamu</h4>
                        <p>Tamu diterima di area ruang tamu bersama. Jam bertamu maksimal pukul 22.00 WIB demi menjaga ketenangan dan kenyamanan seluruh penghuni kost.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1">4. Kebersihan & Pemeliharaan Fasilitas</h4>
                        <p>Penghuni wajib menjaga kebersihan kamar masing-masing serta fasilitas umum. Kerusakan inventaris kamar akibat kelalaian penghuni menjadi tanggung jawab penghuni terkait.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1">5. Larangan Keras</h4>
                        <p>Dilarang keras membawa, mengonsumsi, atau mengedarkan minuman keras, narkoba, senjata tajam/api, melakukan aktivitas asusila, perjudian, serta kegiatan yang melanggar norma hukum dan meresahkan lingkungan.</p>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm mb-1">6. Uang Jaminan (Deposit)</h4>
                        <p>Deposit jaminan disimpan selama masa sewa berlangsung dan akan dikembalikan secara penuh saat kontrak berakhir dan kamar diserahterimakan dalam keadaan baik dan bersih.</p>
                    </div>
                </div>

                <!-- Agreement Action / Audit Box -->
                @if ($contract->isAgreementAccepted())
                    <div class="mt-5 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-200 flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        <div>
                            <p class="font-bold">Persetujuan Digital Telah Terekam</p>
                            <p class="text-[11px] opacity-90 mt-0.5">
                                Disetujui oleh <strong>{{ $contract->agreementAcceptedBy?->name ?? $tenant->name }}</strong> pada {{ $contract->agreement_accepted_at?->translatedFormat('d F Y, H:i') }} (Versi: {{ $contract->agreement_version ?? 'v1.0' }}).
                            </p>
                        </div>
                    </div>
                @else
                    <form method="POST" action="{{ route('portal.contract.agreement', $contract) }}" class="mt-5 space-y-4">
                        @csrf
                        <div class="flex items-start gap-3">
                            <input type="checkbox"
                                   name="terms_accepted"
                                   id="terms_accepted"
                                   value="1"
                                   required
                                   class="mt-1 w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 dark:border-slate-700 dark:bg-slate-900">
                            <label for="terms_accepted" class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed cursor-pointer select-none">
                                Saya telah membaca, memahami, dan menyetujui seluruh isi <strong>Tata Tertib & Ketentuan Sewa Kost</strong> di atas tanpa paksaan dari pihak manapun.
                            </label>
                        </div>
                        @error('terms_accepted')
                            <p class="text-red-500 text-xs">{{ $message }}</p>
                        @enderror

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Setujui Tata Tertib & Ketentuan Sewa
                        </button>
                    </form>
                @endif
            </div>

            <!-- Initial Invoice & Payment Info Card -->
            @if ($initialInvoice)
                <div class="p-6 sm:p-8 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Tagihan Pembayaran Awal</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">Tagihan sewa bulan pertama untuk mengaktifkan kontrak</p>
                        </div>
                        <div>
                            <x-ui.badge :status="$initialInvoice->status">
                                {{ $initialInvoice->status->label() }}
                            </x-ui.badge>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <span class="text-xs text-slate-400">Total Tagihan Awal</span>
                            <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-0.5">
                                Rp {{ number_format($initialInvoice->total_amount, 0, ',', '.') }}
                            </p>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">
                                Jatuh Tempo: {{ $initialInvoice->due_date->format('d M Y') }}
                            </span>
                        </div>
                        
                        @if ($initialInvoice->status === \App\Enums\StatusTagihan::Paid)
                            <div class="flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Pembayaran Lunas
                            </div>
                        @else
                            <div class="flex items-center gap-3">
                                <a href="{{ route('portal.invoices.show', $initialInvoice) }}"
                                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition-colors">
                                    <span>Bayar Tagihan Sekarang</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Bank Accounts -->
                    @if ($bankAccounts->isNotEmpty())
                        <div class="mt-5">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-3">Rekening Pembayaran Kost:</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($bankAccounts as $bank)
                                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 flex items-center justify-between">
                                        <div>
                                            <p class="text-xs font-bold text-slate-900 dark:text-white">{{ $bank->bank_name }}</p>
                                            <p class="text-sm font-mono font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $bank->account_number }}</p>
                                            <p class="text-[11px] text-slate-500 dark:text-slate-400">a.n {{ $bank->account_holder }}</p>
                                        </div>
                                        <div class="text-xs font-bold text-slate-400">
                                            Transfer Bank
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        @endif
    </div>
</x-portal-layout>
