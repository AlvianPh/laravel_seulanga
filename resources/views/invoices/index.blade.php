<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Manajemen Tagihan') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <x-ui.card>

                @if (session('success'))
                    <x-ui.alert type="success">
                        {{ session('success') }}
                    </x-ui.alert>
                @endif
                @if (session('info'))
                    <x-ui.alert type="info">
                        {{ session('info') }}
                    </x-ui.alert>
                @endif

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Manajemen Tagihan Bulanan</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Pantau status penagihan sewa, tanggal jatuh tempo, dan rincian denda</p>
                    </div>
                    
                    <!-- Manual Generate Button -->
                    <form method="POST" action="{{ route('invoices.generate-manual') }}" class="w-full sm:w-auto">
                        @csrf
                        <div class="flex flex-wrap items-center gap-2">
                            <x-ui.select name="month" class="px-2.5 py-1.5 text-xs">
                                @for($m=1; $m<=12; $m++)
                                    <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                                @endfor
                            </x-ui.select>
                            <x-ui.select name="year" class="px-2.5 py-1.5 text-xs">
                                <option value="{{ date('Y') }}" selected>{{ date('Y') }}</option>
                                <option value="{{ date('Y')+1 }}">{{ date('Y')+1 }}</option>
                            </x-ui.select>
                            <x-ui.button type="submit" variant="primary" size="sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                Generate Tagihan
                            </x-ui.button>
                        </div>
                    </form>
                </div>

                <!-- Filter & Search -->
                <form method="GET" action="{{ route('invoices.index') }}" class="mb-6 flex flex-col sm:flex-row gap-3 items-center" x-data x-ref="form">
                    <div class="flex-1 w-full">
                        <x-ui.input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Cari nama penghuni atau kamar..." class="text-sm" />
                    </div>
                    <div class="w-full sm:w-32">
                        <x-ui.select name="month" @change="$refs.form.submit()" class="text-sm">
                            <option value="">Semua Bln</option>
                            @for($m=1; $m<=12; $m++)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Bulan {{ $m }}</option>
                            @endfor
                        </x-ui.select>
                    </div>
                    <div class="w-full sm:w-32">
                        <x-ui.select name="year" @change="$refs.form.submit()" class="text-sm">
                            <option value="">Semua Thn</option>
                            <option value="2026" {{ request('year') == '2026' ? 'selected' : '' }}>2026</option>
                            <option value="2027" {{ request('year') == '2027' ? 'selected' : '' }}>2027</option>
                        </x-ui.select>
                    </div>
                    <div class="w-full sm:w-40">
                        <x-ui.select name="status" @change="$refs.form.submit()" class="text-sm">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" {{ request('status') === $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </x-ui.select>
                    </div>
                    <div class="flex gap-2 w-full sm:w-auto">
                        <x-ui.button type="submit" variant="primary" size="sm" class="flex-1 sm:flex-none">
                            Cari
                        </x-ui.button>
                        @if(request()->anyFilled(['search', 'status', 'month', 'year']))
                            <x-ui.button href="{{ route('invoices.index') }}" variant="secondary" size="sm">Reset</x-ui.button>
                        @endif
                    </div>
                </form>

                <!-- Table -->
                <x-ui.table-wrapper>
                    <x-slot name="header">
                        <tr>
                            <th class="px-4 py-3.5">ID</th>
                            <th class="px-4 py-3.5">Periode</th>
                            <th class="px-4 py-3.5">Penghuni / Kamar</th>
                            <th class="px-4 py-3.5">Total Tagihan</th>
                            <th class="px-4 py-3.5">Jatuh Tempo</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </x-slot>
                            @forelse ($invoices as $invoice)
                                <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-700/40 transition-colors">
                                    <td class="px-4 py-3.5 font-mono text-xs text-gray-500 dark:text-gray-400">#INV-{{ $invoice->id }}</td>
                                    <td class="px-4 py-3.5 font-bold text-gray-900 dark:text-white">{{ $invoice->month }}/{{ $invoice->year }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $invoice->tenant->name ?? 'Dihapus' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Kamar {{ $invoice->room->room_number ?? 'Dihapus' }}</div>
                                    </td>
                                    <td class="px-4 py-3.5 font-bold text-indigo-600 dark:text-indigo-400">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3.5 text-xs font-semibold {{ $invoice->status->value === 'overdue' ? 'text-rose-600 dark:text-rose-400' : 'text-gray-700 dark:text-gray-300' }}">{{ $invoice->due_date->format('d M Y') }}</td>
                                    <td class="px-4 py-3.5">
                                        <x-ui.badge :status="$invoice->status">{{ $invoice->status->label() }}</x-ui.badge>
                                    </td>
                                    <td class="px-4 py-3.5 text-right space-x-1.5 whitespace-nowrap">
                                        @php
                                            $phone = $invoice->tenant->phone ?? '';
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                            if (str_starts_with($cleanPhone, '0')) {
                                                $cleanPhone = '62' . substr($cleanPhone, 1);
                                            }
                                            $waText = rawurlencode("Halo Kak " . ($invoice->tenant->name ?? '') . ",\n\nIni pengingat tagihan sewa Kamar " . ($invoice->room->room_number ?? '') . " periode " . $invoice->month . "/" . $invoice->year . " sebesar Rp " . number_format($invoice->total_amount, 0, ',', '.') . ".\nJatuh tempo: " . $invoice->due_date->format('d M Y') . ".\n\nMohon lakukan pembayaran dan konfirmasi. Terima kasih! 🙏");
                                        @endphp
                                        @if($cleanPhone && $invoice->status->value !== 'paid')
                                            <a href="https://wa.me/{{ $cleanPhone }}?text={{ $waText }}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/60 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors" title="Kirim Tagihan via WhatsApp">
                                                <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                                <span>WA Tagihan</span>
                                            </a>
                                        @endif
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('invoices.show', $invoice) }}">Detail</x-ui.button>
                                        <x-ui.button size="sm" variant="secondary" href="{{ route('invoices.edit', $invoice) }}">Edit</x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <x-ui.empty-state colspan="7" icon="invoice" title="Belum Ada Tagihan" description="Belum ada tagihan sewa bulanan yang dibuat untuk periode ini." />
                            @endforelse
                </x-ui.table-wrapper>

                <div class="mt-6">
                    {{ $invoices->links() }}
                </div>

        </x-ui.card>
    </div>
</x-app-layout>
