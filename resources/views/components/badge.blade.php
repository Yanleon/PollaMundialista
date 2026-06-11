@props([
    'variant' => 'info',
])

@php
    $variants = [
        'info' => 'bg-rose-500/15 text-rose-200 ring-rose-400/35',
        'success' => 'bg-emerald-500/15 text-emerald-200 ring-emerald-400/35',
        'warning' => 'bg-amber-400/15 text-amber-200 ring-amber-300/35',
        'danger' => 'bg-red-600/20 text-red-200 ring-red-400/35',
        'muted' => 'bg-slate-800/80 text-slate-300 ring-slate-600/50',
    ];

    $classes = $variants[$variant] ?? $variants['info'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {$classes}"]) }}>
    {{ $slot }}
</span>
