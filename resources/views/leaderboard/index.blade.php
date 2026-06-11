<x-app-layout>
    @php
        $ranking = $ranking ?? collect([
            (object) ['name' => 'Laura Gomez', 'department' => 'Finanzas', 'total_points' => 42, 'exact_scores' => 6, 'correct_results' => 11],
            (object) ['name' => 'Carlos Ruiz', 'department' => 'TI', 'total_points' => 40, 'exact_scores' => 5, 'correct_results' => 10],
            (object) ['name' => 'Daniela Perez', 'department' => 'Operaciones', 'total_points' => 38, 'exact_scores' => 5, 'correct_results' => 9],
            (object) ['name' => 'Miguel Nunez', 'department' => 'Comercial', 'total_points' => 35, 'exact_scores' => 4, 'correct_results' => 8],
        ]);
    @endphp

    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h1 class="section-title">Ranking general</h1>
                <p class="section-subtitle">Desempate por marcadores exactos y resultados acertados.</p>
            </div>
            <x-badge variant="warning">Top 3 destacado</x-badge>
        </div>
    </x-slot>

    @php
        $hasPrizes = collect($prizes ?? [])->contains(fn ($prize) => filled($prize['name'] ?? null) || filled($prize['image_path'] ?? null));
    @endphp

    @if ($hasPrizes)
        <div class="mb-6 rounded-3xl border border-amber-400/30 bg-gradient-to-br from-amber-500/15 via-slate-900 to-rose-500/10 p-5 shadow-[0_0_35px_rgba(251,191,36,0.08)]">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-200">Premios top 3</p>
                    <h2 class="mt-1 text-2xl font-bold text-white">{{ $canViewSecretPrizes ? 'Premios revelados' : 'Premios secretos' }}</h2>
                    <p class="mt-1 text-sm text-slate-300">
                        @if ($canViewSecretPrizes && ! $prizesAreRevealed)
                            Vista privada de administrador. Los participantes aun ven los premios ocultos.
                        @elseif ($canViewSecretPrizes)
                            Los premios ya estan visibles para todos.
                        @elseif ($prizesRevealAt)
                            Se destapan el {{ $prizesRevealAt->format('d/m/Y') }}, dia de la final.
                        @else
                            El admin los destapara el dia de la final.
                        @endif
                    </p>
                </div>
                <x-badge variant="warning">{{ $canViewSecretPrizes ? 'Visible' : 'Secreto' }}</x-badge>
            </div>

            <div class="mt-5 grid gap-3 md:grid-cols-3">
                @foreach ([1 => 'Primer lugar', 2 => 'Segundo lugar', 3 => 'Tercer lugar'] as $place => $label)
                    <div class="rounded-2xl border border-slate-700/80 bg-slate-950/60 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $label }}</p>

                        @if ($canViewSecretPrizes && ($prizes[$place]['image_path'] ?? null))
                            <img src="{{ asset('storage/'.$prizes[$place]['image_path']) }}" alt="Imagen premio {{ strtolower($label) }}" class="mt-3 h-36 w-full rounded-xl object-cover">
                        @elseif (! $canViewSecretPrizes)
                            <div class="mt-3 flex h-36 w-full items-center justify-center rounded-xl border border-dashed border-amber-300/30 bg-amber-400/10 text-sm font-semibold text-amber-100">Imagen secreta</div>
                        @endif

                        <p class="mt-2 text-lg font-bold text-slate-100">
                            {{ $canViewSecretPrizes ? (($prizes[$place]['name'] ?? null) ?: 'Sin premio definido') : 'Premio secreto' }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <x-ranking-table :entries="$ranking" />
</x-app-layout>
