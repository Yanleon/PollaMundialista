@props([
    'team' => null,
    'size' => 'sm',
])

@php
    $flagUrl = data_get($team, 'flag_url');
    $code = data_get($team, 'code', '---');
    $name = data_get($team, 'name', 'Equipo');

    $classes = match ($size) {
        'md' => 'h-7 w-10 text-[10px]',
        'lg' => 'h-9 w-12 text-xs',
        default => 'h-5 w-7 text-[9px]',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center overflow-hidden rounded border border-slate-600 bg-slate-800 align-middle font-bold text-slate-300']) }}>
    @if ($flagUrl)
        <img src="{{ $flagUrl }}" alt="Bandera de {{ $name }}" loading="lazy" class="{{ $classes }} object-cover" onerror="this.classList.add('hidden'); this.nextElementSibling.classList.remove('hidden');">
        <span class="{{ $classes }} hidden items-center justify-center px-1">{{ $code }}</span>
    @else
        <span class="{{ $classes }} inline-flex items-center justify-center px-1">{{ $code }}</span>
    @endif
</span>
