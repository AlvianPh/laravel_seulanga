<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dokumen & Berkas Penghuni') }}
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

        <x-ui.card>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">Daftar Berkas Dokumen</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Kelola verifikasi berkas identitas KTP dan dokumen persyaratan penghuni.</p>
                </div>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('documents.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penghuni / judul berkas..."
                           class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                </div>

                <div>
                    <select name="tenant_id" class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        <option value="">Semua Penghuni</option>
                        @foreach ($tenants as $t)
                            <option value="{{ $t->id }}" {{ request('tenant_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="type" class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        <option value="">Semua Jenis Dokumen</option>
                        @foreach ($types as $tp)
                            <option value="{{ $tp->value }}" {{ request('type') === $tp->value ? 'selected' : '' }}>
                                {{ $tp->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <select name="status" class="w-full text-xs rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2">
                        <option value="">Semua Status</option>
                        @foreach ($statuses as $st)
                            <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shrink-0">
                        Filter
                    </button>
                    @if(request()->anyFilled(['search', 'tenant_id', 'type', 'status']))
                        <a href="{{ route('documents.index') }}" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 text-xs shrink-0" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700 text-[11px] font-bold text-gray-400 uppercase tracking-wider">
                            <th class="py-3 px-3">Penghuni</th>
                            <th class="py-3 px-3">Judul Dokumen</th>
                            <th class="py-3 px-3">Jenis</th>
                            <th class="py-3 px-3">Berkas</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3">Tanggal Unggah</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse ($documents as $doc)
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="py-3.5 px-3">
                                    <span class="font-bold text-gray-900 dark:text-white block">{{ $doc->tenant->name ?? '-' }}</span>
                                    <span class="text-xs text-gray-400">{{ $doc->tenant->phone ?? '-' }}</span>
                                </td>
                                <td class="py-3.5 px-3 font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $doc->title }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <x-ui.badge color="{{ $doc->type->color() }}">
                                        {{ $doc->type->label() }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="text-xs text-gray-600 dark:text-gray-400 block truncate max-w-[140px]">{{ $doc->file_name }}</span>
                                    <span class="text-[11px] text-gray-400">{{ $doc->formattedSize() }}</span>
                                </td>
                                <td class="py-3.5 px-3">
                                    <x-ui.badge color="{{ $doc->status->color() }}">
                                        {{ $doc->status->label() }}
                                    </x-ui.badge>
                                </td>
                                <td class="py-3.5 px-3 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $doc->created_at->translatedFormat('d M Y H:i') }}
                                </td>
                                <td class="py-3.5 px-3 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('documents.preview', $doc) }}" target="_blank"
                                       class="inline-flex items-center p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"
                                       title="Pratinjau Berkas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('documents.download', $doc) }}"
                                       class="inline-flex items-center p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400"
                                       title="Unduh Berkas">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                    <a href="{{ route('documents.show', $doc) }}"
                                       class="inline-flex items-center px-2.5 py-1 bg-indigo-50 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-300 font-semibold rounded-lg text-xs hover:bg-indigo-100 transition-colors">
                                        Detail / Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500 dark:text-gray-400">
                                    Tidak ada dokumen berkas yang sesuai filter.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($documents->hasPages())
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    {{ $documents->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-app-layout>
