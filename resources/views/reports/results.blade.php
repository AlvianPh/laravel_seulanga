<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between md:items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $title }}
            </h2>
            <div class="mt-2 md:mt-0 text-sm text-gray-500">
                Periode: <span class="font-bold text-gray-700 dark:text-gray-300">{{ $dateLabel }}</span>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700/70 overflow-hidden">
            
            <!-- Action Bar -->
            <div class="p-4 sm:p-5 bg-gray-50/70 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-700/70 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Periode Laporan: <strong class="text-gray-700 dark:text-gray-200">{{ $dateLabel }}</strong></p>
                </div>

                <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end">
                    <x-ui.button variant="secondary" size="sm" href="{{ route('reports.index') }}">
                        &larr; Filter Ulang
                    </x-ui.button>

                    <!-- Tombol Export -->
                    <form method="POST" action="{{ route('reports.generate') }}" class="flex items-center gap-2">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="filter" value="{{ $filter }}">
                        <input type="hidden" name="start_date" value="{{ $start_date }}">
                        <input type="hidden" name="end_date" value="{{ $end_date }}">

                        <x-ui.button type="submit" name="action" value="pdf" variant="danger" size="sm">
                            Export PDF
                        </x-ui.button>
                        <x-ui.button type="submit" name="action" value="excel" variant="primary" size="sm">
                            Excel (XLSX)
                        </x-ui.button>
                        <x-ui.button type="submit" name="action" value="csv" variant="secondary" size="sm">
                            CSV
                        </x-ui.button>
                    </form>
                </div>
            </div>

            <!-- Table Content -->
            <div class="p-4 sm:p-6 overflow-x-auto">
                @include($viewName, ['data' => $data])
            </div>

        </div>
    </div>
</x-app-layout>
