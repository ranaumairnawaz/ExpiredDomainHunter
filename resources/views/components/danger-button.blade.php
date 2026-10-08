<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest shadow-md hover:from-red-600 hover:to-rose-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 active:scale-95 transition-all duration-150']) }}>
    {{ $slot }}
</button>
