@props([
    'padding' => true,
])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200/80 dark:border-gray-700/70 overflow-hidden text-gray-900 dark:text-gray-100 transition-all duration-200 ' . ($padding ? 'p-4 sm:p-6 lg:p-7' : '')]) }}>
    {{ $slot }}
</div>
