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
        'success' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-300 border-emerald-200/80 dark:border-emerald-800/50',
        'info' => 'bg-sky-50 text-sky-700 dark:bg-sky-950/50 dark:text-sky-300 border-sky-200/80 dark:border-sky-800/50',
        'warning' => 'bg-amber-50 text-amber-800 dark:bg-amber-950/50 dark:text-amber-300 border-amber-200/80 dark:border-amber-800/50',
        'danger' => 'bg-rose-50 text-rose-700 dark:bg-rose-950/50 dark:text-rose-300 border-rose-200/80 dark:border-rose-800/50',
        'purple' => 'bg-violet-50 text-violet-700 dark:bg-violet-950/50 dark:text-violet-300 border-violet-200/80 dark:border-violet-800/50',
        default => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border-slate-200 dark:border-slate-700', // neutral
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border tracking-wide {$variantClasses}"]) }}>
    <span class="w-1.5 h-1.5 rounded-full mr-1.5 opacity-80 {{ match($mappedVariant) {
        'success' => 'bg-emerald-500',
        'info' => 'bg-sky-500',
        'warning' => 'bg-amber-500',
        'danger' => 'bg-rose-500',
        'purple' => 'bg-violet-500',
        default => 'bg-slate-400',
    } }}"></span>
    {{ $slot }}
</span>
