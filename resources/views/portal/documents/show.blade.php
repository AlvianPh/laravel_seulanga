<x-portal-layout>
    <div class="space-y-6 max-w-3xl mx-auto">
        <!-- Header & Breadcrumb -->
        <div>
            <a href="{{ route('portal.documents.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-emerald-600 dark:text-slate-400 dark:hover:text-emerald-400 mb-2 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Kembali ke Daftar Dokumen
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $document->title }}
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                        Diupload pada {{ $document->created_at->translatedFormat('d F Y H:i') }}
                    </p>
                </div>
                @php
                    $badgeColor = match($document->status) {
                        \App\Enums\DocumentStatus::Verified => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800',
                        \App\Enums\DocumentStatus::Rejected => 'bg-rose-50 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400 border border-rose-200 dark:border-rose-800',
                        default => 'bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400 border border-amber-200 dark:border-amber-800',
                    };
                @endphp
                <div>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeColor }}">
                        {{ $document->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        @if ($document->status === \App\Enums\DocumentStatus::Rejected && $document->rejection_reason)
            <!-- Rejection Reason Alert -->
            <div class="p-5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/60 text-rose-900 dark:text-rose-200 flex items-start gap-3">
                <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <h3 class="text-sm font-bold">Catatan Penolakan dari Pengelola Kost</h3>
                    <p class="text-xs text-rose-700 dark:text-rose-300 mt-1">
                        {{ $document->rejection_reason }}
                    </p>
                </div>
            </div>
        @endif

        <!-- Document Details Card -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-400 block mb-1">Jenis Dokumen</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $document->type->label() }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Nama Berkas Asli</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200 text-sm truncate block">{{ $document->file_name }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Ukuran Berkas</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $document->formattedSize() }} ({{ $document->mime_type }})</span>
                </div>
                <div>
                    <span class="text-slate-400 block mb-1">Status Verifikasi</span>
                    <span class="font-semibold text-slate-700 dark:text-slate-300">
                        @if ($document->status === \App\Enums\DocumentStatus::Verified)
                            Diverifikasi pada {{ $document->verified_at ? $document->verified_at->translatedFormat('d M Y H:i') : '-' }}
                        @elseif ($document->status === \App\Enums\DocumentStatus::Rejected)
                            Ditolak pada {{ $document->verified_at ? $document->verified_at->translatedFormat('d M Y H:i') : '-' }}
                        @else
                            Menunggu pemeriksaan oleh staff/admin kost
                        @endif
                    </span>
                </div>
            </div>

            @if ($document->description)
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-xs">
                    <span class="text-slate-400 block mb-1">Keterangan / Catatan</span>
                    <p class="text-slate-700 dark:text-slate-300 leading-relaxed">{{ $document->description }}</p>
                </div>
            @endif

            <!-- File Actions -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('portal.documents.preview', $document) }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-semibold text-xs transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Buka Pratinjau
                    </a>
                    <a href="{{ route('portal.documents.download', $document) }}"
                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Unduh Berkas
                    </a>
                </div>

                @can('delete', $document)
                    <form method="POST" action="{{ route('portal.documents.destroy', $document) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/40 text-xs font-semibold transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Dokumen
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>
</x-portal-layout>
