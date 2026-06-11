@props([
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $variants = [
        'primary' => 'bg-gradient-to-r from-white via-white to-red-500 text-slate-950 hover:scale-[1.02] hover:shadow-[0_0_24px_rgba(255,31,69,0.42)]',
        'success' => 'bg-emerald-500 text-slate-950 hover:bg-emerald-400',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-500',
        'secondary' => 'border border-slate-700 bg-slate-900/80 text-slate-100 hover:border-rose-500/60 hover:text-rose-200',
    ];

    $classes = $variants[$variant] ?? $variants['primary'];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full px-4 py-2 text-sm font-semibold transition duration-300 {$classes}"]) }}>
    {{ $slot }}
</button>
