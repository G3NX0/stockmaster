<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-8 py-4 zen-glass rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] italic text-onyx-600 dark:text-onyx-400 hover:bg-black/5 dark:hover:bg-white/5 active:scale-95 transition-all duration-300']) }}>
    {{ $slot }}
</button>
