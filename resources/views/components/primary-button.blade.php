<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full bg-gradient-to-r from-white via-white to-rose-500 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-slate-950 transition duration-300 hover:scale-[1.02] hover:shadow-[0_0_22px_rgba(255,31,69,0.35)] focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-0']) }}>
    {{ $slot }}
</button>
