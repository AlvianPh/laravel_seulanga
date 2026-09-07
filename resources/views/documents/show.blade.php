<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail & Verifikasi Dokumen Penghuni') }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
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

        <div class="flex items-center justify-between">
            <a href="{{ route('documents.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Dokumen
            </a>
            <div class="flex items-center gap-2">
                <x-ui.badge color="{{ $document->status->color() }}">
                    {{ $document->status->label() }}
                </x-ui.badge>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Document Details & Preview -->
            <div class="md:col-span-2 space-y-6">
                <x-ui.card>
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 mb-1.5">
                                {{ $document->type->label() }}
                            </span>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $document->title }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Diunggah pada {{ $document->created_at->translatedFormat('d F Y H:i') }} oleh {{ $document->uploader->name ?? '-' }}
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-xs">
                        <div>
                            <span class="text-gray-400 block mb-1">Nama Berkas</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200 truncate block">{{ $document->file_name }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-1">Ukuran</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $document->formattedSize() }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-1">MIME Type</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200">{{ $document->mime_type }}</span>
                        </div>
                    </div>

                    @if ($document->description)
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 text-xs">
                            <span class="text-gray-400 block mb-1 font-semibold">Keterangan Tambahan:</span>
                            <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $document->description }}</p>
                        </div>
                    @endif

                    @if ($document->status === \App\Enums\DocumentStatus::Rejected && $document->rejection_reason)
                        <div class="mt-4 p-4 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-xs text-rose-800 dark:text-rose-200">
                            <strong>Alasan Penolakan:</strong> {{ $document->rejection_reason }}
                            <div class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">
                                Ditolak oleh {{ $document->verifier->name ?? 'Staff' }} pada {{ $document->verified_at ? $document->verified_at->translatedFormat('d M Y H:i') : '-' }}
                            </div>
                        </div>
                    @elseif ($document->status === \App\Enums\DocumentStatus::Verified)
                        <div class="mt-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/50 text-xs text-emerald-800 dark:text-emerald-200">
                            <strong>Telah Diverifikasi:</strong> Dokumen dinyatakan sah dan valid.
                            <div class="text-[11px] text-emerald-600 dark:text-emerald-400 mt-1">
                                Diverifikasi oleh {{ $document->verifier->name ?? 'Staff' }} pada {{ $document->verified_at ? $document->verified_at->translatedFormat('d M Y H:i') : '-' }}
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('documents.preview', $document) }}" target="_blank"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold text-xs transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Buka Pratinjau
                            </a>
                            <a href="{{ route('documents.download', $document) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                Unduh Berkas Asli
                            </a>
                        </div>

                        <form method="POST" action="{{ route('documents.destroy', $document) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-rose-500 hover:text-rose-700 hover:bg-rose-50 dark:hover:bg-rose-950/50 rounded-xl transition-colors" title="Hapus Dokumen">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </x-ui.card>
            </div>

            <!-- Right Col: Tenant Info & Review Action Form -->
            <div class="space-y-6">
                <!-- Tenant Card -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Informasi Penghuni</h4>
                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-gray-400 block mb-0.5">Nama Lengkap</span>
                            <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $document->tenant->name ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">No. Telepon</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $document->tenant->phone ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block mb-0.5">Kamar Saat Ini</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $document->tenant->currentContract()?->room?->room_number ? 'Kamar '.$document->tenant->currentContract()->room->room_number : 'Belum Ada Kamar Aktif' }}
                            </span>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('tenants.show', $document->tenant) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline font-semibold flex items-center gap-1">
                                Lihat Profil Penghuni
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </x-ui.card>

                <!-- Review Action Form -->
                <x-ui.card>
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Tindakan Verifikasi</h4>
                    <form method="POST" action="{{ route('documents.review', $document) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Keputusan</label>
                            <div class="grid grid-cols-2 gap-2">
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer text-xs font-medium">
                                    <input type="radio" name="action" value="verify" {{ old('action', 'verify') === 'verify' ? 'checked' : '' }}
                                           class="text-indigo-600 focus:ring-indigo-500">
                                    <span>Verifikasi Sah</span>
                                </label>
                                <label class="flex items-center gap-2 p-2.5 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer text-xs font-medium">
                                    <input type="radio" name="action" value="reject" {{ old('action') === 'reject' ? 'checked' : '' }}
                                           class="text-rose-600 focus:ring-rose-500">
                                    <span>Tolak Berkas</span>
                                </label>
                            </div>
                            @error('action')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="rejection_reason" class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                                Alasan Penolakan <span class="text-gray-400 font-normal">(wajib jika ditolak)</span>
                            </label>
                            <textarea id="rejection_reason" name="rejection_reason" rows="3" placeholder="Jelaskan alasan dokumen ditolak (misal: foto buram, NIK tidak terbaca)..."
                                      class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 focus:ring-indigo-500">{{ old('rejection_reason', $document->rejection_reason) }}</textarea>
                            @error('rejection_reason')
                                <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-all hover:scale-[1.01]">
                            Simpan Keputusan
                        </button>
                    </form>
                </x-ui.card>
            </div>
        </div>
    </div>
</x-app-layout>
