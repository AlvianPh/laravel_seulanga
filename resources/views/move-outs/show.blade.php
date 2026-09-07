<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Proses Keluar Kost (Move-Out)') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        @if (session('success'))
            <x-ui.alert type="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif
        @if (session('error'))
            <x-ui.alert type="error">
                {{ session('error') }}
            </x-ui.alert>
        @endif
        @if ($errors->any())
            <x-ui.alert type="error">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-ui.alert>
        @endif

        <!-- Back & Title -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <x-ui.button href="{{ route('move-outs.index') }}" variant="secondary" size="xs" class="mb-2">
                    &larr; Kembali ke Daftar
                </x-ui.button>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Move-Out: {{ $moveOut->tenant?->name ?? 'Penghuni' }}
                    </h3>
                    <x-ui.badge :status="$moveOut->status">
                        {{ $moveOut->status->label() }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Details & Step Actions (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Pengajuan Card -->
                <x-ui.card>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                        Informasi Permohonan Move-Out
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-4">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Penghuni</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm mt-0.5 inline-block">
                                {{ $moveOut->tenant?->name ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Kamar & Lantai</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm mt-0.5 inline-block">
                                Kamar {{ $moveOut->contract?->room?->room_number ?? '-' }} (Lantai {{ $moveOut->contract?->room?->floor ?? '-' }})
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Rencana Tanggal Keluar</span>
                            <span class="font-bold text-rose-600 dark:text-rose-400 text-sm mt-0.5 inline-block">
                                {{ $moveOut->requested_move_out_date->format('d M Y') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Waktu Pengajuan</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-sm mt-0.5 inline-block">
                                {{ $moveOut->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <!-- Reason -->
                    <div class="mb-4">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold mb-1">Alasan Keluar</span>
                        <div class="p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 text-gray-800 dark:text-gray-200 text-sm leading-relaxed whitespace-pre-line border border-gray-200/60 dark:border-gray-700/60">
                            {{ $moveOut->reason }}
                        </div>
                    </div>

                    @if ($moveOut->notes)
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold mb-1">Catatan Tambahan Penghuni</span>
                            <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800/60 text-gray-700 dark:text-gray-300 text-xs leading-relaxed whitespace-pre-line border border-gray-200/60 dark:border-gray-700/60">
                                {{ $moveOut->notes }}
                            </div>
                        </div>
                    @endif
                </x-ui.card>

                <!-- Step 1: Review Action (Jika status pending) -->
                @if ($moveOut->isPending())
                    @can('review', $moveOut)
                        <x-ui.card>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                                Tahap 1: Tinjau Permohonan Move-Out
                            </h4>

                            <form action="{{ route('move-outs.review', $moveOut) }}" method="POST"
                                  x-data="{ action: 'approve', note: '' }"
                                  class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                        Keputusan <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label :class="action === 'approve' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-950/30 text-indigo-700 dark:text-indigo-300' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'"
                                               class="border-2 rounded-xl p-3 flex items-center justify-center gap-2 cursor-pointer font-semibold text-sm transition">
                                            <input type="radio" name="action" value="approve" x-model="action" class="sr-only">
                                            <span>&#10003; Setujui Move-Out</span>
                                        </label>

                                        <label :class="action === 'reject' ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'"
                                               class="border-2 rounded-xl p-3 flex items-center justify-center gap-2 cursor-pointer font-semibold text-sm transition">
                                            <input type="radio" name="action" value="reject" x-model="action" class="sr-only">
                                            <span>&#10007; Tolak Move-Out</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label for="review_note" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                        Catatan Review <span x-show="action === 'reject'" class="text-rose-500 font-bold">* (Wajib jika menolak)</span><span x-show="action === 'approve'" class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <textarea name="review_note" id="review_note" rows="3" x-model="note"
                                              :required="action === 'reject'"
                                              placeholder="Tuliskan catatan review atau alasan penolakan..."
                                              class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('review_note') }}</textarea>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <x-ui.button type="submit" ::variant="action === 'approve' ? 'primary' : 'danger'" size="sm">
                                        <span x-text="action === 'approve' ? 'Konfirmasi Setujui Permohonan' : 'Konfirmasi Tolak Permohonan'"></span>
                                    </x-ui.button>
                                </div>
                            </form>
                        </x-ui.card>
                    @endcan
                @endif

                <!-- Step 2: Inspeksi Kamar (Jika status approved atau inspection) -->
                @if ($moveOut->isApproved() || $moveOut->isInspection())
                    @can('inspect', $moveOut)
                        <x-ui.card>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                                Tahap 2: Catat Hasil Inspeksi Fisik Kamar
                            </h4>

                            <form action="{{ route('move-outs.inspect', $moveOut) }}" method="POST" class="space-y-4">
                                @csrf

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="room_condition" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                            Kondisi Kamar <span class="text-rose-500">*</span>
                                        </label>
                                        <select name="room_condition" id="room_condition" required
                                                class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="baik" {{ old('room_condition', $moveOut->room_condition) === 'baik' ? 'selected' : '' }}>Baik / Bersih / Siap Huni</option>
                                            <option value="perbaikan_ringan" {{ old('room_condition', $moveOut->room_condition) === 'perbaikan_ringan' ? 'selected' : '' }}>Perbaikan Ringan (Kran/Cat/Lampu)</option>
                                            <option value="perbaikan_berat" {{ old('room_condition', $moveOut->room_condition) === 'perbaikan_berat' ? 'selected' : '' }}>Perbaikan Berat / Kerusakan Fasilitas</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="damage_cost" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                            Estimasi Biaya Kerusakan (Rp)
                                        </label>
                                        <input type="number" name="damage_cost" id="damage_cost" min="0" step="1"
                                               value="{{ old('damage_cost', (int)$moveOut->damage_cost) }}"
                                               placeholder="0 jika tidak ada kerusakan"
                                               class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <p class="text-[11px] text-gray-400 mt-1">Biaya ini akan menjadi pengurang (deduction) dari nilai deposit.</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="damage_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                        Rincian Kerusakan / Fasilitas yang Hilang <span class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <textarea name="damage_notes" id="damage_notes" rows="2"
                                              placeholder="Contoh: Kunci pintu hilang, remote AC rusak, stopkontak pecah..."
                                              class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('damage_notes', $moveOut->damage_notes) }}</textarea>
                                </div>

                                <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="requires_room_maintenance" value="1"
                                               {{ old('requires_room_maintenance', $moveOut->requires_room_maintenance) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-xs font-semibold text-gray-800 dark:text-gray-200">
                                            Kamar memerlukan perbaikan fisik (Ubah status kamar menjadi <em>Maintenance</em> setelah kontrak selesai)
                                        </span>
                                    </label>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <x-ui.button type="submit" variant="primary" size="sm">
                                        Simpan Hasil Inspeksi
                                    </x-ui.button>
                                </div>
                            </form>
                        </x-ui.card>
                    @endcan
                @endif

                <!-- Step 3: Perhitungan Settlement & Finalisasi Kontrak -->
                <x-ui.card>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                        Audit Finansial & Settlement Deposit
                    </h4>

                    <div class="space-y-3 text-xs mb-6">
                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">(+) Nilai Deposit Awal Kontrak:</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm">
                                Rp {{ number_format($settlement['deposit_amount'], 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">(-) Total Sisa Tagihan Belum Lunas:</span>
                            <span class="font-bold text-rose-600 dark:text-rose-400 text-sm">
                                Rp {{ number_format($settlement['outstanding_invoices_amount'], 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between py-2 border-b border-gray-100 dark:border-gray-800">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">(-) Potongan Biaya Kerusakan Inspeksi:</span>
                            <span class="font-bold text-rose-600 dark:text-rose-400 text-sm">
                                Rp {{ number_format($settlement['damage_deduction_amount'], 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Result Highlight -->
                        <div class="p-4 rounded-xl {{ $settlement['remaining_tenant_liability'] > 0 ? 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200' : 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200' }} mt-4">
                            @if ($settlement['remaining_tenant_liability'] > 0)
                                <div class="flex justify-between items-center text-rose-800 dark:text-rose-200">
                                    <span class="font-bold uppercase tracking-wider text-xs">Sisa Kewajiban Tagihan Penghuni:</span>
                                    <span class="font-extrabold text-base">
                                        Rp {{ number_format($settlement['remaining_tenant_liability'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">
                                    Nilai deposit habis terpakai dan penghuni masih memiliki kewajiban pembayaran yang harus diselesaikan.
                                </p>
                            @else
                                <div class="flex justify-between items-center text-emerald-800 dark:text-emerald-200">
                                    <span class="font-bold uppercase tracking-wider text-xs">Deposit yang Harus Dikembalikan ke Penghuni:</span>
                                    <span class="font-extrabold text-base">
                                        Rp {{ number_format($settlement['deposit_returned_amount'], 0, ',', '.') }}
                                    </span>
                                </div>
                                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1">
                                    Deposit masih mencukupi. Saldo di atas dikembalikan ke rekening penghuni.
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Finalize Form if not completed -->
                    @if ($moveOut->canBeFinalized() && ! $moveOut->isCompleted())
                        @can('finalize', $moveOut)
                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                <h5 class="text-xs font-bold uppercase tracking-wider text-gray-900 dark:text-white mb-2">
                                    Konfirmasi Penyelesaian Move-Out & Penutupan Kontrak
                                </h5>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    Aksi ini akan menandai move-out <strong>Selesai (Completed)</strong>, mengubah status kontrak menjadi <strong>Ended</strong>, dan membebaskan kamar menjadi <strong>{{ $moveOut->requires_room_maintenance ? 'Maintenance' : 'Available' }}</strong>.
                                </p>

                                <form action="{{ route('move-outs.finalize', $moveOut) }}" method="POST"
                                      onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan proses move-out dan menutup kontrak sewa ini?');"
                                      class="space-y-3">
                                    @csrf

                                    <div>
                                        <label for="settlement_notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1">
                                            Catatan Settlement / Pengembalian Deposit <span class="text-gray-400 font-normal">(Opsional)</span>
                                        </label>
                                        <textarea name="settlement_notes" id="settlement_notes" rows="2"
                                                  placeholder="Contoh: Deposit ditransfer ke Rekening BCA 123456 an Budi..."
                                                  class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('settlement_notes', $moveOut->settlement_notes) }}</textarea>
                                    </div>

                                    <div class="flex justify-end">
                                        <x-ui.button type="submit" variant="primary" size="sm">
                                            Selesaikan Move-Out & Tutup Kontrak
                                        </x-ui.button>
                                    </div>
                                </form>
                            </div>
                        @endcan
                    @elseif ($moveOut->isCompleted())
                        <div class="p-3 rounded-xl bg-gray-50 dark:bg-gray-800 text-xs text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-700">
                            <strong>Status:</strong> Kontrak telah diakhiri pada {{ $moveOut->completed_at?->format('d M Y, H:i') }} WIB oleh {{ $moveOut->completer?->name ?? 'Staf' }}.
                            @if ($moveOut->settlement_notes)
                                <p class="mt-1"><strong>Catatan:</strong> {{ $moveOut->settlement_notes }}</p>
                            @endif
                        </div>
                    @endif
                </x-ui.card>
            </div>

            <!-- Right Column: Room & Tenant Summary (1 col) -->
            <div class="space-y-6">
                <!-- Data Penghuni Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                        Informasi Penghuni
                    </h4>
                    @if ($moveOut->tenant)
                        <div class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                            <div>
                                <span class="block text-gray-400 font-semibold">Nama:</span>
                                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $moveOut->tenant->name }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-400 font-semibold">No HP:</span>
                                <span>{{ $moveOut->tenant->phone }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-400 font-semibold">Email:</span>
                                <span>{{ $moveOut->tenant->email ?? '-' }}</span>
                            </div>
                            <div class="pt-2">
                                <x-ui.button href="{{ route('tenants.show', $moveOut->tenant) }}" variant="secondary" size="xs" class="w-full">
                                    Lihat Profil Penghuni
                                </x-ui.button>
                            </div>
                        </div>
                    @endif
                </x-ui.card>

                <!-- Data Kamar Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                        Informasi Kamar & Kontrak
                    </h4>
                    @if ($moveOut->room)
                        <div class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Nomor Kamar:</span>
                                <span class="font-bold text-gray-900 dark:text-white">Kamar {{ $moveOut->room->room_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Lantai:</span>
                                <span>Lantai {{ $moveOut->room->floor }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Status Kamar Saat Ini:</span>
                                <span class="capitalize font-semibold text-gray-900 dark:text-white">{{ $moveOut->room->status->label() }}</span>
                            </div>
                            <div class="pt-2 border-t border-gray-100 dark:border-gray-800 flex justify-between">
                                <span>Status Kontrak:</span>
                                <x-ui.badge :status="$moveOut->contract->status">
                                    {{ $moveOut->contract->status->label() }}
                                </x-ui.badge>
                            </div>
                            <div class="pt-2">
                                <x-ui.button href="{{ route('rooms.show', $moveOut->room) }}" variant="secondary" size="xs" class="w-full">
                                    Lihat Detail Kamar
                                </x-ui.button>
                            </div>
                        </div>
                    @endif
                </x-ui.card>

                <!-- Audit Log Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                        Audit Log
                    </h4>
                    <div class="text-[11px] space-y-1.5 text-gray-500 dark:text-gray-400">
                        <div>Diajukan pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $moveOut->created_at->format('d/m/Y H:i') }}</span></div>
                        @if ($moveOut->reviewed_at)
                            <div>Ditinjau pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $moveOut->reviewed_at->format('d/m/Y H:i') }}</span> ({{ $moveOut->reviewer?->name ?? '-' }})</div>
                        @endif
                        @if ($moveOut->inspected_at)
                            <div>Inspeksi pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $moveOut->inspected_at->format('d/m/Y H:i') }}</span> ({{ $moveOut->inspector?->name ?? '-' }})</div>
                        @endif
                        @if ($moveOut->completed_at)
                            <div>Selesai pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $moveOut->completed_at->format('d/m/Y H:i') }}</span> ({{ $moveOut->completer?->name ?? '-' }})</div>
                        @endif
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
