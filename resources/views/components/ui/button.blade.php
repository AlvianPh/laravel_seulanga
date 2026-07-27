@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-sm';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 text-xs gap-1.5',
        'lg' => 'px-6 py-3 text-base gap-2',
        default => 'px-4 py-2 text-sm gap-2', // md
    };

    $variantClasses = match ($variant) {
        'secondary' => 'bg-gray-200 hover:bg-gray-300 text-gray-700 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600 focus:ring-gray-500 border border-gray-300 dark:border-gray-600',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white dark:bg-red-600 dark:hover:bg-red-500 focus:ring-red-500 border border-transparent',
        'success' => 'bg-green-600 hover:bg-green-700 text-white dark:bg-green-600 dark:hover:bg-green-500 focus:ring-green-500 border border-transparent',
        'warning' => 'bg-yellow-500 hover:bg-yellow-600 text-white dark:bg-yellow-600 dark:hover:bg-yellow-500 focus:ring-yellow-500 border border-transparent',
        default => 'bg-indigo-600 hover:bg-indigo-700 text-white dark:bg-indigo-600 dark:hover:bg-indigo-500 focus:ring-indigo-500 border border-transparent', // primary
    };

    $classes = "{$baseClasses} {$sizeClasses} {$variantClasses}";
@endphp

@if ($attributes->has('href'))
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
