@props([
    'type' => 'info',
    'dismissible' => true,
    'autoDismiss' => true,
    'duration' => 5000,
])

@php
    $typeClasses = match ($type) {
        'success' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 border border-green-300 dark:border-green-800',
        'error', 'danger' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 border border-red-300 dark:border-red-800',
        'warning' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border border-yellow-300 dark:border-yellow-800',
        default => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-300 dark:border-blue-800', // info
    };
@endphp

<div 
    x-data="{ show: true }" 
    x-show="show" 
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @if ($autoDismiss) x-init="setTimeout(() => show = false, {{ $duration }})" @endif
    {{ $attributes->merge(['class' => "p-4 rounded-lg {$typeClasses} mb-4 flex items-center justify-between"]) }}>
    <div class="flex-1">
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button type="button" @click="show = false" class="ml-3 inline-flex text-current hover:opacity-75 focus:outline-none" aria-label="Close">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    @endif
</div>
