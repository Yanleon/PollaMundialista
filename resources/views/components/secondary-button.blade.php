<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center rounded-full border border-slate-700 bg-slate-900/80 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-200 transition duration-200 hover:border-rose-500/60 hover:text-rose-200 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-0 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
