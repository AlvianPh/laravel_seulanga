@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'font-medium text-xs text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 p-3 rounded-xl border border-emerald-200/80 dark:border-emerald-800/60 shadow-xs']) }}>
        {{ $status }}
    </div>
@endif
