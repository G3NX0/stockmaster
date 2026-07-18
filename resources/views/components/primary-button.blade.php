<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-8 py-4 bg-onyx-950 dark:bg-white text-white dark:text-black border-none rounded-2xl font-black text-[10px] uppercase tracking-[0.3em] italic hover:scale-105 active:scale-95 transition-all duration-300 shadow-2xl']) }}>
    {{ $slot }}
</button>
