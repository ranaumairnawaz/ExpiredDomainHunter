@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-2.5 text-sm shadow-sm focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow disabled:opacity-50 disabled:cursor-not-allowed']) }}>
