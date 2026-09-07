<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Permohonan Izin') }}
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
                <x-ui.button href="{{ route('permissions.index') }}" variant="secondary" size="xs" class="mb-2">
                    &larr; Kembali ke Daftar
                </x-ui.button>
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{ $permission->title }}
                    </h3>
                    <x-ui.badge :status="$permission->status">
                        {{ $permission->status->label() }}
                    </x-ui.badge>
                    <x-ui.badge variant="info">
                        {{ $permission->type->label() }}
                    </x-ui.badge>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Request Details & Action (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Info Permohonan Card -->
                <x-ui.card>
                    <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                        Rincian Permohonan Izin
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs mb-4">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Penghuni Pemohon</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm mt-0.5 inline-block">
                                {{ $permission->tenant?->name ?? 'Anonim' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Kamar</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm mt-0.5 inline-block">
                                Kamar {{ $permission->contract?->room?->room_number ?? '-' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Jenis Izin</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-sm mt-0.5 inline-block">
                                {{ $permission->type->label() }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold">Waktu Diajukan</span>
                            <span class="font-medium text-gray-800 dark:text-gray-200 text-sm mt-0.5 inline-block">
                                {{ $permission->created_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                    </div>

                    <!-- Period Info -->
                    <div class="p-3.5 mb-5 rounded-xl bg-indigo-50 dark:bg-indigo-950/30 border border-indigo-100 dark:border-indigo-900/50 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-[11px] font-bold text-indigo-700 dark:text-indigo-400 uppercase tracking-wider block">
                                Periode Permohonan Izin
                            </span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $permission->start_at->format('d M Y, H:i') }} s/d {{ $permission->end_at->format('d M Y, H:i') }} WIB
                            </span>
                        </div>
                        <div class="text-xs text-indigo-700 dark:text-indigo-300 font-medium">
                            Durasi: {{ $permission->start_at->diffForHumans($permission->end_at, true) }}
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-5">
                        <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold mb-1">Keterangan / Alasan</span>
                        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/60 text-gray-800 dark:text-gray-200 text-sm whitespace-pre-line leading-relaxed border border-gray-200/60 dark:border-gray-700/60">
                            {{ $permission->description }}
                        </div>
                    </div>

                    <!-- Review Decision Info if already reviewed -->
                    @if (!$permission->isPending())
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider block font-semibold mb-2">Hasil Keputusan</span>
                            <div class="p-4 rounded-xl {{ $permission->isApproved() ? 'bg-emerald-50 dark:bg-emerald-950/40 border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200' : ($permission->isRejected() ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200' : 'bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-800 dark:text-gray-300') }} border text-sm">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-bold">Status: {{ $permission->status->label() }}</span>
                                </div>
                                @if ($permission->reviewer)
                                    <p class="text-xs text-gray-600 dark:text-gray-300">
                                        Ditinjau oleh <strong>{{ $permission->reviewer->name }}</strong> pada {{ $permission->reviewed_at?->format('d M Y, H:i') }} WIB
                                    </p>
                                @endif
                                @if ($permission->review_note)
                                    <div class="mt-2 pt-2 border-t border-current/20 text-xs">
                                        <strong>Catatan Review:</strong>
                                        <p class="mt-0.5 whitespace-pre-line">{{ $permission->review_note }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </x-ui.card>

                <!-- Review Action Card for Pending Request -->
                @if ($permission->isPending())
                    @can('review', $permission)
                        <x-ui.card>
                            <h4 class="text-base font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-3 mb-4">
                                Tinjau Permohonan Izin
                            </h4>

                            <form action="{{ route('permissions.review', $permission) }}" method="POST"
                                  x-data="{ action: 'approve', note: '' }"
                                  class="space-y-4">
                                @csrf

                                <div>
                                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                                        Keputusan <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label :class="action === 'approve' ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'"
                                               class="border-2 rounded-xl p-3 flex items-center justify-center gap-2 cursor-pointer font-semibold text-sm transition">
                                            <input type="radio" name="action" value="approve" x-model="action" class="sr-only">
                                            <span>&#10003; Setujui Izin</span>
                                        </label>

                                        <label :class="action === 'reject' ? 'border-rose-500 bg-rose-50 dark:bg-rose-950/30 text-rose-700 dark:text-rose-300' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800'"
                                               class="border-2 rounded-xl p-3 flex items-center justify-center gap-2 cursor-pointer font-semibold text-sm transition">
                                            <input type="radio" name="action" value="reject" x-model="action" class="sr-only">
                                            <span>&#10007; Tolak Izin</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <label for="review_note" class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                        Catatan Review <span x-show="action === 'reject'" class="text-rose-500 font-bold">* (Wajib diisi jika menolak)</span><span x-show="action === 'approve'" class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <textarea name="review_note" id="review_note" rows="3" x-model="note"
                                              :required="action === 'reject'"
                                              placeholder="Tuliskan catatan atau alasan penolakan..."
                                              class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('review_note') }}</textarea>
                                </div>

                                <div class="pt-2 flex justify-end">
                                    <x-ui.button type="submit" ::variant="action === 'approve' ? 'primary' : 'danger'" size="sm">
                                        <span x-text="action === 'approve' ? 'Konfirmasi Setujui Izin' : 'Konfirmasi Tolak Izin'"></span>
                                    </x-ui.button>
                                </div>
                            </form>
                        </x-ui.card>
                    @endcan
                @endif
            </div>

            <!-- Right Column: Room & Tenant Summary (1 col) -->
            <div class="space-y-6">
                <!-- Data Penghuni Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-wider border-b border-gray-200 dark:border-gray-700 pb-2 mb-3">
                        Informasi Penghuni
                    </h4>
                    @if ($permission->tenant)
                        <div class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                            <div>
                                <span class="block text-gray-400 font-semibold">Nama:</span>
                                <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $permission->tenant->name }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-400 font-semibold">No HP:</span>
                                <span>{{ $permission->tenant->phone }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-400 font-semibold">Email:</span>
                                <span>{{ $permission->tenant->email ?? '-' }}</span>
                            </div>
                            <div class="pt-2">
                                <x-ui.button href="{{ route('tenants.show', $permission->tenant) }}" variant="secondary" size="xs" class="w-full">
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
                        Informasi Kamar & Kontrak
                    </h4>
                    @if ($permission->room)
                        <div class="text-xs space-y-2 text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Nomor Kamar:</span>
                                <span class="font-bold text-gray-900 dark:text-white">Kamar {{ $permission->room->room_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Lantai:</span>
                                <span>Lantai {{ $permission->room->floor }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tipe Kamar:</span>
                                <span>{{ $permission->room->roomType?->name ?? 'Standard' }}</span>
                            </div>
                            @if ($permission->contract)
                                <div class="flex justify-between pt-1 border-t border-gray-100 dark:border-gray-800">
                                    <span>Nomor Kontrak:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $permission->contract->contract_number }}</span>
                                </div>
                            @endif
                            <div class="pt-2">
                                <x-ui.button href="{{ route('rooms.show', $permission->room) }}" variant="secondary" size="xs" class="w-full">
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
                        <div>Diajukan pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $permission->created_at->format('d/m/Y H:i') }}</span></div>
                        @if ($permission->reviewed_at)
                            <div>Ditinjau pada: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $permission->reviewed_at->format('d/m/Y H:i') }}</span></div>
                            <div>Reviewer: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $permission->reviewer?->name ?? '-' }}</span></div>
                        @endif
                    </div>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
