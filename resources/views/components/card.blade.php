@props([
    'title' => null,
    'subtitle' => null,
])

<section {{ $attributes->merge(['class' => 'glass-panel p-5']) }}>
    @if ($title || $subtitle)
        <header class="mb-4">
            @if ($title)
                <h3 class="text-base font-semibold text-slate-100">{{ $title }}</h3>
            @endif

            @if ($subtitle)
                <p class="mt-1 text-sm text-slate-400">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
