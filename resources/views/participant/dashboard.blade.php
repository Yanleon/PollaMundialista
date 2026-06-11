<x-app-layout>
    @php
        $upcomingMatches = $upcomingMatches ?? collect([
            (object) ['phase' => 'Fase de grupos', 'group_name' => 'A', 'home_team_name' => 'Colombia', 'away_team_name' => 'Brasil', 'match_date' => '21/06/2026 18:00', 'prediction_deadline' => '21/06/2026 17:50', 'status' => 'scheduled'],
            (object) ['phase' => 'Fase de grupos', 'group_name' => 'B', 'home_team_name' => 'Argentina', 'away_team_name' => 'Uruguay', 'match_date' => '22/06/2026 20:00', 'prediction_deadline' => '22/06/2026 19:50', 'status' => 'live'],
        ]);

        $pendingPredictions = $pendingPredictions ?? 4;
        $userPoints = $userPoints ?? 18;
        $rankingPosition = $rankingPosition ?? 7;
        $bracketRounds = $bracketRounds ?? collect();
        $finishedBracketMatches = $finishedBracketMatches ?? collect();
        $prizes = $prizes ?? [];
        $prizesAreRevealed = $prizesAreRevealed ?? false;

        $finalRound = $bracketRounds->firstWhere('key', 'final');
        $finalMatch = data_get($finalRound, 'matches.0');

        $championName = null;

        if ($finalMatch && $finalMatch->status === 'finished' && $finalMatch->home_score !== null && $finalMatch->away_score !== null) {
            if ($finalMatch->home_score > $finalMatch->away_score) {
                $championName = $finalMatch->homeTeam?->name;
            } elseif ($finalMatch->away_score > $finalMatch->home_score) {
                $championName = $finalMatch->awayTeam?->name;
            }
        }

        $thirdPlaceMatch = $finishedBracketMatches->first(function ($match) {
            $phase = \Illuminate\Support\Str::lower((string) ($match->phase ?? ''));

            return str_contains($phase, 'tercer') || str_contains($phase, 'third');
        });
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="section-title">Panel del participante</h1>
                <p class="section-subtitle">Consulta tus pendientes, puntaje y progreso en el ranking.</p>
            </div>
            <x-badge variant="info">{{ now()->format('d/m/Y') }}</x-badge>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Puntos acumulados</p>
                <p class="mt-2 text-3xl font-black text-slate-100">{{ $userPoints }}</p>
            </x-card>

            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Pronosticos pendientes</p>
                <p class="mt-2 text-3xl font-black text-slate-100">{{ $pendingPredictions }}</p>
            </x-card>

            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Posicion ranking</p>
                <p class="mt-2 text-3xl font-black text-rose-300">{{ $rankingPosition ? '#'.$rankingPosition : 'N/A' }}</p>
            </x-card>

            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Partidos prox. 4 dias</p>
                <p class="mt-2 text-3xl font-black text-slate-100">{{ $upcomingMatches->count() }}</p>
            </x-card>
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-amber-400/30 bg-gradient-to-br from-slate-950 via-slate-900 to-rose-950/70 p-5 shadow-[0_0_42px_rgba(251,191,36,0.08)]">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-200">Premios top 3</p>
                        <h2 class="mt-1 text-2xl font-black text-white">{{ $prizesAreRevealed ? 'Premios destapados' : 'Premios bajo llave' }}</h2>
                        <p class="mt-1 text-sm text-slate-300">
                            @if ($prizesAreRevealed)
                                Ya puedes ver los premios oficiales para los tres primeros lugares.
                            @elseif ($prizesRevealAt)
                                Se destapan el {{ $prizesRevealAt->format('d/m/Y') }}, dia de la final.
                            @else
                                El admin los destapara cuando llegue la final.
                            @endif
                        </p>
                    </div>
                    <x-badge variant="warning">{{ $prizesAreRevealed ? 'Revelados' : 'Secretos' }}</x-badge>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ([1 => 'Primer lugar', 2 => 'Segundo lugar', 3 => 'Tercer lugar'] as $place => $label)
                        <article class="relative overflow-hidden rounded-3xl border border-slate-700/80 bg-slate-950/70 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">{{ $label }}</p>

                            @if ($prizesAreRevealed && ($prizes[$place]['image_path'] ?? null))
                                <img src="{{ asset('storage/'.$prizes[$place]['image_path']) }}" alt="Imagen premio {{ strtolower($label) }}" class="mt-3 h-40 w-full rounded-2xl object-cover">
                            @elseif ($prizesAreRevealed)
                                <div class="mt-3 flex h-40 w-full items-center justify-center rounded-2xl bg-slate-800 text-sm text-slate-400">Sin imagen</div>
                            @else
                                <div class="mt-3 relative h-40 w-full overflow-hidden rounded-2xl border border-amber-300/30 bg-[radial-gradient(circle_at_50%_35%,rgba(251,191,36,0.28),transparent_34%),linear-gradient(135deg,rgba(15,23,42,0.95),rgba(88,28,135,0.5),rgba(15,23,42,0.95))]">
                                    <div class="absolute inset-0 backdrop-blur-sm"></div>
                                    <div class="absolute inset-x-0 top-1/2 h-10 -translate-y-1/2 rotate-[-8deg] bg-amber-300/90 shadow-[0_0_24px_rgba(251,191,36,0.35)]"></div>
                                    <div class="absolute inset-x-0 top-1/2 h-10 -translate-y-1/2 rotate-[8deg] bg-rose-500/85 shadow-[0_0_24px_rgba(244,63,94,0.35)]"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <span class="rounded-full border border-white/20 bg-slate-950/80 px-4 py-2 text-sm font-black uppercase tracking-[0.18em] text-white">Tapado</span>
                                    </div>
                                </div>
                            @endif

                            <p class="mt-3 text-lg font-black text-slate-100">
                                {{ $prizesAreRevealed ? (($prizes[$place]['name'] ?? null) ?: 'Sin premio definido') : 'Premio secreto' }}
                            </p>

                            @unless ($prizesAreRevealed)
                                <p class="mt-1 text-xs text-slate-400">Solo se revelara al llegar la final.</p>
                            @endunless
                        </article>
                    @endforeach
                </div>
        </section>

        <section>
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-100">Cuadro eliminatorio profesional</h2>
                    <p class="text-sm text-slate-300">Visualiza el avance por rondas con estado en tiempo real.</p>
                </div>
                <a href="{{ route('participant.predictions.index') }}" class="text-sm font-semibold text-rose-300 hover:text-rose-200">Ir a pronosticos</a>
            </div>

            @if ($bracketRounds->isEmpty())
                <x-card>
                    <p class="text-sm text-slate-300">Aun no hay cruces de eliminacion cargados para mostrar el cuadro.</p>
                </x-card>
            @else
                <div class="bracket-poster">
                    <div class="bracket-deco-layer" aria-hidden="true">
                        <img src="{{ asset('references/mascota_zayu.png') }}" alt="" class="bracket-mascot mascot-zayu">
                        <img src="{{ asset('references/mascota_clutch.png') }}" alt="" class="bracket-mascot mascot-clutch">
                        <img src="{{ asset('references/mascota_maple.png') }}" alt="" class="bracket-mascot mascot-maple">
                    </div>

                    <div class="bracket-poster-head">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Llave final</p>
                        <h3 class="text-3xl font-semibold text-white md:text-5xl">Fase Final</h3>
                    </div>

                    <div class="bracket-layout">
                        @foreach ($bracketRounds as $round)
                            <section class="bracket-column bracket-column-{{ $round['key'] }}">
                                <header class="bracket-column-head">
                                    <h4 class="text-xs uppercase tracking-[0.14em] text-slate-100">{{ $round['label'] }}</h4>
                                    <p class="text-[11px] text-slate-400">Ronda de {{ $round['matches']->count() }}</p>
                                </header>

                                <div class="bracket-column-body">
                                    @foreach ($round['matches'] as $matchGame)
                                        @if ($matchGame)
                                            @php
                                                $statusVariant = match($matchGame->status) {
                                                    'finished' => 'success',
                                                    'live' => 'warning',
                                                    'scheduled' => 'info',
                                                    'cancelled' => 'danger',
                                                    default => 'muted',
                                                };
                                            @endphp

                                            <article class="bracket-slot {{ $loop->odd ? 'slot-odd' : 'slot-even' }}">
                                                <p class="bracket-date">{{ $matchGame->match_date?->format('D, d M | H:i') }}</p>
                                                <div class="bracket-team-row">
                                                    <span class="flex min-w-0 items-center gap-2 truncate text-sm font-semibold text-slate-100"><x-team-flag :team="$matchGame->homeTeam" /> <span class="truncate">{{ $matchGame->homeTeam?->name ?? 'Por definir' }}</span></span>
                                                    @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                                        <span class="text-sm font-bold text-rose-300">{{ $matchGame->home_score }}</span>
                                                    @endif
                                                </div>
                                                <div class="bracket-team-row">
                                                    <span class="flex min-w-0 items-center gap-2 truncate text-sm font-semibold text-slate-100"><x-team-flag :team="$matchGame->awayTeam" /> <span class="truncate">{{ $matchGame->awayTeam?->name ?? 'Por definir' }}</span></span>
                                                    @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                                        <span class="text-sm font-bold text-rose-300">{{ $matchGame->away_score }}</span>
                                                    @endif
                                                </div>
                                                <div class="mt-2"><x-badge :variant="$statusVariant" class="text-[10px]">{{ $matchGame->status }}</x-badge></div>
                                            </article>
                                        @else
                                            <article class="bracket-slot bracket-slot-empty {{ $loop->odd ? 'slot-odd' : 'slot-even' }}">
                                                <p class="text-center text-xs text-slate-400">Cruce pendiente</p>
                                            </article>
                                        @endif
                                    @endforeach
                                </div>
                            </section>
                        @endforeach

                        <aside class="bracket-sidepanel">
                            <div class="bracket-side-block">
                                <p class="text-xs uppercase tracking-[0.14em] text-slate-300">Tercer puesto</p>
                                @if ($thirdPlaceMatch)
                                    <p class="mt-2 text-sm font-semibold text-white">{{ $thirdPlaceMatch->homeTeam?->name }} vs {{ $thirdPlaceMatch->awayTeam?->name }}</p>
                                    <p class="mt-1 text-sm font-bold text-rose-300">{{ $thirdPlaceMatch->home_score }} - {{ $thirdPlaceMatch->away_score }}</p>
                                @else
                                    <p class="mt-2 text-sm text-slate-400">Sin resultado registrado</p>
                                @endif
                            </div>

                            <div class="bracket-side-block">
                                <p class="text-xs uppercase tracking-[0.14em] text-slate-300">Final</p>
                                @if ($finalMatch)
                                    <div class="mt-2 space-y-1 text-sm font-semibold text-white">
                                        <p class="flex items-center gap-2"><x-team-flag :team="$finalMatch->homeTeam" /> {{ $finalMatch->homeTeam?->name }}</p>
                                        <p class="flex items-center gap-2"><x-team-flag :team="$finalMatch->awayTeam" /> {{ $finalMatch->awayTeam?->name }}</p>
                                    </div>
                                    @if ($finalMatch->status === 'finished' && $finalMatch->home_score !== null && $finalMatch->away_score !== null)
                                        <p class="mt-1 text-sm font-bold text-rose-300">{{ $finalMatch->home_score }} - {{ $finalMatch->away_score }}</p>
                                    @else
                                        <p class="mt-1 text-xs text-slate-400">Pendiente de jugar</p>
                                    @endif
                                @else
                                    <p class="mt-2 text-sm text-slate-400">Cruce no definido</p>
                                @endif
                            </div>

                            <div class="bracket-side-block">
                                <p class="text-xs uppercase tracking-[0.14em] text-slate-300">Campeon</p>
                                <p class="mt-2 text-xl font-black text-amber-300">{{ $championName ?? 'Por definir' }}</p>
                            </div>
                        </aside>
                    </div>
                </div>
            @endif
        </section>

        <section class="grid gap-6 xl:grid-cols-[1.35fr_1fr]">
            <x-card>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-100">Finalizados y goles</h2>
                    <x-badge variant="success">Actualizado</x-badge>
                </div>

                <x-table :headers="['Partido', 'Fase', 'Fecha', 'Marcador']">
                    @forelse ($finishedBracketMatches as $finishedMatch)
                        <tr class="transition hover:bg-slate-800/60">
                            <td class="px-4 py-3 text-sm font-semibold text-slate-100">{{ $finishedMatch->homeTeam?->name }} vs {{ $finishedMatch->awayTeam?->name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-300">{{ $finishedMatch->phase }}</td>
                            <td class="px-4 py-3 text-sm text-slate-300">{{ $finishedMatch->match_date?->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 text-sm font-bold text-rose-300">{{ $finishedMatch->home_score }} - {{ $finishedMatch->away_score }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-5 text-center text-sm text-slate-400">No hay marcadores finales registrados en eliminacion.</td>
                        </tr>
                    @endforelse
                </x-table>
            </x-card>

            <x-card>
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-100">Proximos partidos</h2>
                    <a href="{{ route('participant.predictions.index') }}" class="text-sm font-semibold text-rose-300 hover:text-rose-200">Ver todos</a>
                </div>

                @if ($upcomingMatches->isEmpty())
                    <p class="text-sm text-slate-300">No hay partidos disponibles para pronosticar en este momento.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($upcomingMatches->take(6) as $matchGame)
                            <article class="tournament-mini-match">
                                <p class="truncate text-sm font-semibold text-slate-100">{{ $matchGame->homeTeam?->name }} vs {{ $matchGame->awayTeam?->name }}</p>
                                <div class="mt-1 flex items-center justify-between text-xs text-slate-400">
                                    <span>{{ $matchGame->phase }}</span>
                                    <span>{{ $matchGame->match_date?->format('d/m H:i') }}</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </section>
    </div>
</x-app-layout>
