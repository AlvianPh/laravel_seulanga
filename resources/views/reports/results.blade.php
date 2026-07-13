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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                
                <!-- Action Bar -->
                <div class="p-4 bg-gray-50 dark:bg-gray-700 border-b flex justify-between items-center flex-wrap gap-4">
                    <a href="{{ route('reports.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900 dark:text-gray-300">
                        &larr; Kembali ke Filter
                    </a>

                    <!-- Tombol Export -->
                    <form method="POST" action="{{ route('reports.generate') }}" class="flex space-x-2">
                        @csrf
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="filter" value="{{ $filter }}">
                        <input type="hidden" name="start_date" value="{{ $start_date }}">
                        <input type="hidden" name="end_date" value="{{ $end_date }}">

                        <button type="submit" name="action" value="pdf" class="px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded hover:bg-red-700 flex items-center">
                            PDF
                        </button>
                        <button type="submit" name="action" value="excel" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded hover:bg-green-700 flex items-center">
                            Excel (XLSX)
                        </button>
                        <button type="submit" name="action" value="csv" class="px-4 py-2 bg-gray-600 text-white text-sm font-semibold rounded hover:bg-gray-700 flex items-center">
                            CSV
                        </button>
                    </form>
                </div>

                <!-- Table Content -->
                <div class="p-6 overflow-x-auto">
                    @include($viewName, ['data' => $data])
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
