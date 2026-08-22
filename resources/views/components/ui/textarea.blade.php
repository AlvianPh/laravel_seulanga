@props(['disabled' => false])

<textarea @disabled($disabled) {{ $attributes->merge(['class' => 'w-full border border-gray-300 dark:border-gray-600/80 rounded-xl shadow-xs px-3.5 py-2.5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500 focus:border-indigo-600 dark:focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:focus:ring-indigo-500/25 focus:outline-none text-sm transition-all duration-150']) }}>{{ $slot }}</textarea>
