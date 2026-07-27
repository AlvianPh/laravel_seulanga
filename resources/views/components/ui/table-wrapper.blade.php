<div {{ $attributes->merge(['class' => 'overflow-x-auto']) }}>
    <table class="w-full text-sm text-left">
        @isset($header)
            <thead class="bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 border-b dark:border-gray-600">
                {{ $header }}
            </thead>
        @endisset
        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            {{ $slot }}
        </tbody>
    </table>
</div>
