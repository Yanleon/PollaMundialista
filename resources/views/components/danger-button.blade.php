<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center rounded-full border border-rose-400/60 bg-rose-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-200 hover:bg-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-0']) }}>
    {{ $slot }}
</button>
