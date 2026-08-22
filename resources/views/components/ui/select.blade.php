@props([
    'disabled' => false,
    'error' => null,
])

@php
    $name = $attributes->get('name');
    $dotName = $name ? str_replace(['[', ']'], ['.', ''], rtrim($name, '[]')) : null;
    $hasError = false;

    if ($error === true || (is_string($error) && $error !== '')) {
        $hasError = true;
    } elseif ($error === false) {
        $hasError = false;
    } elseif (isset($errors) && $errors instanceof \Illuminate\Support\ViewErrorBag) {
        if ($name && $errors->has($name)) {
            $hasError = true;
        } elseif ($dotName && $errors->has($dotName)) {
            $hasError = true;
        }
    }

    $borderClasses = $hasError
        ? 'border-rose-500 dark:border-rose-500 text-rose-900 dark:text-rose-100 focus:border-rose-500 dark:focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20 dark:focus:ring-rose-500/25'
        : 'border-gray-300 dark:border-gray-600/80 text-gray-900 dark:text-white focus:border-indigo-600 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/25';
@endphp

<select @disabled($disabled) {{ $attributes->merge(['class' => "w-full border rounded-xl shadow-xs px-3.5 py-2.5 bg-white dark:bg-gray-800 focus:outline-none text-sm transition-all duration-150 {$borderClasses}"]) }}>
    {{ $slot }}
</select>
