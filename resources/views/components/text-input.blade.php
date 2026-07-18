@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'zen-glass border-black/5 dark:border-white/5 rounded-2xl px-6 py-4 text-sm font-black tracking-tight focus:ring-0 focus:border-onyx-950 dark:focus:border-white transition-all placeholder:text-onyx-300 dark:placeholder:text-onyx-600']) }}>
