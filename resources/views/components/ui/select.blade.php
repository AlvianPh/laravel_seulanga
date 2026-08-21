@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm px-3 py-2 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 focus:outline-none transition-colors duration-150']) }}>
    {{ $slot }}
</select>
