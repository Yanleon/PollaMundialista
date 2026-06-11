@props([
    'headers' => [],
])

<div class="overflow-hidden rounded-3xl border border-slate-700/80 bg-slate-950/60">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'min-w-full divide-y divide-slate-700/70 bg-slate-900/70']) }}>
            @if (! empty($headers))
                <thead class="bg-slate-900/95">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.12em] text-slate-300">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif

            <tbody class="divide-y divide-slate-700/60">
                {{ $slot }}
            </tbody>
        </table>
    </div>
</div>
