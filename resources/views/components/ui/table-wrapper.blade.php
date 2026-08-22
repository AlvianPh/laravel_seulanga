<div {{ $attributes->merge(['class' => 'overflow-x-auto rounded-xl border border-gray-200/80 dark:border-gray-700/80 -mx-4 sm:mx-0']) }}>
    <table class="w-full text-sm text-left border-collapse min-w-[600px] sm:min-w-full">
        @isset($header)
            <thead class="bg-gray-50/90 dark:bg-gray-900/40 text-gray-600 dark:text-gray-400 border-b border-gray-200/80 dark:border-gray-700/80 text-xs font-semibold uppercase tracking-wider">
                {{ $header }}
            </thead>
        @endisset
        <tbody class="divide-y divide-gray-200/60 dark:divide-gray-700/70 bg-white dark:bg-gray-800/80">
            {{ $slot }}
        </tbody>
    </table>
</div>
