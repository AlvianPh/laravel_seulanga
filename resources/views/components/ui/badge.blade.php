@props([
    'status' => null,
    'variant' => null,
])

@php
    // Jika status dikirim, tentukan variant berdasarkan mapping status
    if ($status) {
        $statusVal = is_object($status) && enum_exists(get_class($status)) ? $status->value : (string) $status;
        $mappedVariant = match (strtolower($statusVal)) {
            'available', 'active', 'paid', 'verified' => 'success',
            'occupied', 'admin' => 'info',
            'pending', 'maintenance' => 'warning',
            'overdue', 'rejected', 'cancelled' => 'danger',
            'ended' => 'neutral',
            'owner' => 'purple',
            default => 'neutral',
        };
    } else {
        $mappedVariant = $variant ?? 'neutral';
    }

    $variantClasses = match ($mappedVariant) {
        'success' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 border border-green-200 dark:border-green-800/50',
        'info' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800/50',
        'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300 border border-yellow-200 dark:border-yellow-800/50',
        'danger' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300 border border-red-200 dark:border-red-800/50',
        'purple' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800/50',
        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600', // neutral
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {$variantClasses}"]) }}>
    {{ $slot }}
</span>
