@props([
    'type' => 'info',
])

@php
    $typeClasses = match ($type) {
        'success' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-800',
        'error', 'danger' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-300 dark:border-red-800',
        'warning' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-800',
        default => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-800', // info
    };
@endphp

<div {{ $attributes->merge(['class' => "p-4 rounded-lg {$typeClasses} mb-4"]) }}>
    {{ $slot }}
</div>
