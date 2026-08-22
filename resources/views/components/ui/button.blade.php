@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-gray-900 disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.98] select-none cursor-pointer';

    $sizeClasses = match ($size) {
        'sm' => 'px-3 py-1.5 sm:py-1 text-xs gap-1.5 min-h-[40px] sm:min-h-[36px]',
        'lg' => 'px-5 py-3 text-base gap-2.5 min-h-[48px] shadow-sm',
        default => 'px-4 py-2.5 text-sm gap-2 min-h-[40px] shadow-sm', // md
    };

    $variantClasses = match ($variant) {
        'secondary' => 'bg-gray-100 hover:bg-gray-200/90 text-gray-700 dark:bg-gray-700/80 dark:text-gray-200 dark:hover:bg-gray-700 focus:ring-gray-400 border border-gray-300/80 dark:border-gray-600',
        'danger' => 'bg-rose-600 hover:bg-rose-700 text-white shadow-rose-500/20 focus:ring-rose-500 border border-transparent',
        'success' => 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-500/20 focus:ring-emerald-500 border border-transparent',
        'warning' => 'bg-amber-500 hover:bg-amber-600 text-white shadow-amber-500/20 focus:ring-amber-500 border border-transparent',
        default => 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-500/25 focus:ring-indigo-500 border border-transparent', // primary
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
