<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Permintaan Perbaikan') }}
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
                <x-ui.button href="{{ route('maintenance.index') }}" variant="secondary" size="xs" class="mb-2">
                    &larr; Kembali ke Daftar
                </x-ui.button>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Tiket #{{ $maintenance->ticket_number }}
                    </h3>
                    <x-ui.badge :status="$maintenance->status">
                        {{ $maintenance->status->label() }}
                    </x-ui.badge>
                    <x-ui.badge :status="$maintenance->priority">
                        {{ $maintenance->priority->label() }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Request Details & Actions (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Keluhan Card -->
                <x-ui.card>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                        Rincian Laporan Perbaikan
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-4">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Penghuni Pelapor</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm mt-0.5 inline-block">
                                {{ $maintenance->tenant?->name ?? 'Anonim' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Kamar & Lokasi</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm mt-0.5 inline-block">
                                Kamar {{ $maintenance->room?->room_number ?? '-' }} &bull; {{ $maintenance->location }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Kategori</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-sm mt-0.5 inline-block">
                                {{ $maintenance->category->label() }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Waktu Dilaporkan</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-sm mt-0.5 inline-block">
                                {{ $maintenance->reported_at ? $maintenance->reported_at->format('d M Y, H:i') : $maintenance->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-5">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold mb-1">Deskripsi Kerusakan</span>
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 text-gray-800 dark:text-gray-200 text-sm whitespace-pre-line leading-relaxed border border-gray-200/60 dark:border-gray-700/60">
                            {{ $maintenance->description }}
                        </div>
                    </div>

                    <!-- Photo Evidence -->
                    @if ($maintenance->photo_path)
                        <div>
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold mb-2">Foto Bukti Kerusakan</span>
                            <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 max-w-md">
                                <a href="{{ Storage::url($maintenance->photo_path) }}" target="_blank" rel="noopener" class="block group relative">
                                    <img src="{{ Storage::url($maintenance->photo_path) }}" alt="Foto Kerusakan" class="w-full max-h-72 object-cover group-hover:opacity-95 transition-opacity">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold">
                                        Buka Gambar Penuh &rarr;
                                    </div>
                                </a>
                            </div>
                        </div>
                    @endif
                </x-ui.card>

                <!-- Update Status Card -->
                <x-ui.card>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                        Perbarui Status & Tindak Lanjut
                    </h4>

                    @if ($maintenance->isResolved() || $maintenance->isRejected())
                        <div class="p-4 rounded-xl {{ $maintenance->isResolved() ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200' : 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200' }} border text-sm">
                            <span class="font-bold block mb-1">
                                Tiket ini telah {{ $maintenance->isResolved() ? 'Selesai Dikerjakan' : 'Ditolak' }}.
                            </span>
                            @if ($maintenance->rejection_reason)
                                <p class="text-xs mt-1"><strong>Alasan Penolakan:</strong> {{ $maintenance->rejection_reason }}</p>
                            @endif
                            @if ($maintenance->notes)
                                <p class="text-xs mt-1"><strong>Catatan Pengerjaan:</strong> {{ $maintenance->notes }}</p>
                            @endif
                        </div>
                    @else
                        <form action="{{ route('maintenance.update-status', $maintenance) }}" method="POST"
                              x-data="{ selectedStatus: '{{ old('status', $maintenance->status->value) }}' }"
                              class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div>
                                <label for="status" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Pilih Status Baru <span class="text-rose-500">*</span>
                                </label>
                                <select name="status" id="status" x-model="selectedStatus" required
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Pilih Status</option>
                                    @foreach ($statuses as $st)
                                        @if ($st !== \App\Enums\StatusMaintenance::Pending)
                                            <option value="{{ $st->value }}" {{ old('status', $maintenance->status->value) === $st->value ? 'selected' : '' }}>
                                                {{ $st->label() }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                    Catatan Teknisi / Penanganan <span class="text-gray-400 font-normal">(Opsional)</span>
                                </label>
                                <textarea name="notes" id="notes" rows="3"
                                          placeholder="Tuliskan catatan teknisi, suku cadang yang diganti, atau instruksi..."
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $maintenance->notes) }}</textarea>
                            </div>

                            <!-- Rejection reason -->
                            <div x-show="selectedStatus === 'rejected'" x-cloak>
                                <label for="rejection_reason" class="block text-xs font-bold uppercase tracking-wider text-rose-700 dark:text-rose-400 mb-1.5">
                                    Alasan Penolakan <span class="text-rose-500">*</span>
                                </label>
                                <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                          placeholder="Jelaskan alasan laporan perbaikan ini tidak dapat diproses..."
                                          class="w-full rounded-xl border-rose-300 dark:border-rose-700 dark:bg-gray-800 text-sm focus:border-rose-500 focus:ring-rose-500">{{ old('rejection_reason', $maintenance->rejection_reason) }}</textarea>
                            </div>

                            <div class="pt-2 flex justify-end">
                                <x-ui.button type="submit" variant="primary" size="sm">
                                    Simpan Perubahan Status
                                </x-ui.button>
                            </div>
                        </form>
                    @endif
                </x-ui.card>

                <!-- Biaya Pengeluaran (Expense Integration) Card -->
                <x-ui.card>
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-gray-200 dark:border-gray-700 pb-3 mb-4 gap-2">
                        <div>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white">
                                Biaya & Pengeluaran Terkait
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Catatan biaya material/teknisi yang terintegrasi dengan modul Pengeluaran (Expense).
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider block">Total Biaya</span>
                            <span class="text-lg font-extrabold text-gray-900 dark:text-white">
                                Rp {{ number_format($maintenance->total_cost, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Linked Expenses List -->
                    @if ($maintenance->expenses->isEmpty())
                        <div class="py-6 text-center text-xs text-gray-500 dark:text-gray-400">
                            Belum ada pengeluaran yang dicatat untuk tiket perbaikan ini.
                        </div>
                    @else
                        <div class="divide-y divide-gray-200 dark:divide-gray-700 mb-4">
                            @foreach ($maintenance->expenses as $exp)
                                <div class="py-3 flex items-center justify-between gap-3 text-xs">
                                    <div>
                                        <span class="font-bold text-gray-900 dark:text-white block">{{ $exp->description }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">
                                            {{ $exp->expenseCategory?->name ?? 'Kategori' }} &bull; {{ $exp->expense_date?->format('d M Y') }}
                                        </span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-semibold text-gray-900 dark:text-white block">
                                            Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                        </span>
                                        @if ($exp->receipt_path)
                                            <a href="{{ Storage::url($exp->receipt_path) }}" target="_blank" rel="noopener" class="text-indigo-600 hover:underline">
                                                Bukti Struk
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add Expense Form Accordion/Modal -->
                    <div x-data="{ openExpenseForm: false }" class="pt-2 border-t border-gray-100 dark:border-gray-800">
                        <div x-show="!openExpenseForm">
                            <x-ui.button @click="openExpenseForm = true" type="button" variant="secondary" size="xs">
                                + Tambah Biaya Perbaikan (Catat Expense)
                            </x-ui.button>
                        </div>

                        <div x-show="openExpenseForm" x-cloak class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/80 border border-gray-200 dark:border-gray-700 mt-2 space-y-3">
                            <div class="flex justify-between items-center">
                                <h5 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider">
                                    Form Catat Pengeluaran Baru
                                </h5>
                                <button type="button" @click="openExpenseForm = false" class="text-gray-400 hover:text-gray-600 text-xs font-semibold">
                                    Tutup
                                </button>
                            </div>

                            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <input type="hidden" name="maintenance_request_id" value="{{ $maintenance->id }}">

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Kategori Biaya</label>
                                        <select name="expense_category_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-xs">
                                            @foreach ($expenseCategories as $ec)
                                                <option value="{{ $ec->id }}" {{ str_contains(strtolower($ec->name), 'perbaikan') || str_contains(strtolower($ec->name), 'repair') ? 'selected' : '' }}>
                                                    {{ $ec->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Nominal (Rp)</label>
                                        <input type="number" name="amount" min="1" step="1" required placeholder="Contoh: 150000"
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-xs">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Tanggal</label>
                                        <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" required
                                               class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Foto Struk / Nota (Opsional)</label>
                                        <input type="file" name="receipt_photo" accept="image/*"
                                               class="w-full text-xs text-gray-500 file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:bg-indigo-50 file:text-indigo-700">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 dark:text-gray-400 mb-1">Deskripsi Pengeluaran</label>
                                    <input type="text" name="description" required
                                           value="Perbaikan {{ $maintenance->category->label() }} (Kamar {{ $maintenance->room?->room_number ?? '-' }}): {{ $maintenance->location }}"
                                           class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-xs">
                                </div>

                                <div class="flex justify-end gap-2 pt-1">
                                    <x-ui.button @click="openExpenseForm = false" type="button" variant="secondary" size="xs">
                                        Batal
                                    </x-ui.button>
                                    <x-ui.button type="submit" variant="primary" size="xs">
                                        Simpan Biaya
                                    </x-ui.button>
                                </div>
                            </form>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <!-- Right Column: Room & Tenant Summary (1 col) -->
            <div class="space-y-6">
                <!-- Data Penghuni Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                        Informasi Penghuni
                    </h4>
                    @if ($maintenance->tenant)
                        <div class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                            <div>
                                <span class="block text-gray-400 font-semibold">Nama:</span>
                                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $maintenance->tenant->name }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-400 font-semibold">No HP:</span>
                                <span>{{ $maintenance->tenant->phone }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-400 font-semibold">Email:</span>
                                <span>{{ $maintenance->tenant->email ?? '-' }}</span>
                            </div>
                            <div class="pt-2">
                                <x-ui.button href="{{ route('tenants.show', $maintenance->tenant) }}" variant="secondary" size="xs" class="w-full">
                                    Lihat Profil Penghuni
                                </x-ui.button>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-500">Data penghuni tidak ditemukan.</p>
                    @endif
                </x-ui.card>

                <!-- Data Kamar Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                        Informasi Kamar
                    </h4>
                    @if ($maintenance->room)
                        <div class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Nomor Kamar:</span>
                                <span class="font-bold text-gray-900 dark:text-white">Kamar {{ $maintenance->room->room_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Lantai:</span>
                                <span>Lantai {{ $maintenance->room->floor }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tipe Kamar:</span>
                                <span>{{ $maintenance->room->roomType?->name ?? 'Standard' }}</span>
                            </div>
                            <div class="pt-2">
                                <x-ui.button href="{{ route('rooms.show', $maintenance->room) }}" variant="secondary" size="xs" class="w-full">
                                    Lihat Detail Kamar
                                </x-ui.button>
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-500">Data kamar tidak ditemukan.</p>
                    @endif
                </x-ui.card>

                <!-- Audit Log Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                        Audit Log
                    </h4>
                    <div class="text-[11px] space-y-1.5 text-gray-500 dark:text-gray-400">
                        <div>Dibuat oleh: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $maintenance->creator?->name ?? 'Sistem' }}</span></div>
                        @if ($maintenance->updater)
                            <div>Terakhir diupdate oleh: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $maintenance->updater->name }}</span></div>
                        @endif
                        @if ($maintenance->resolved_at)
                            <div>Selesai pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $maintenance->resolved_at->format('d/m/Y H:i') }}</span></div>
                        @endif
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
