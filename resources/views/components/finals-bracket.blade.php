@props([
    'bracketRounds' => collect(),
    'finalMatch' => null,
    'championName' => null,
    'thirdPlaceMatch' => null,
    'showThirdPlace' => false,
])

@php
    $rounds = collect($bracketRounds);
    $pathRounds = $rounds->reject(fn ($round) => ($round['key'] ?? null) === 'final')->values();
    $leftRounds = $pathRounds;
    $rightRounds = $pathRounds->reverse()->values();

    $splitRoundMatches = function (array $round, string $side) {
        $slots = (int) ($round['slots'] ?? 0);
        $matches = collect($round['matches'] ?? []);
        $half = max(1, (int) ceil($slots / 2));
        $start = $side === 'left' ? 0 : $half;
        $end = $side === 'left' ? $half - 1 : $slots - 1;

        if ($slots === 0 || $start > $end) {
            return collect();
        }

        return collect(range($start, $end))->map(fn (int $index) => $matches->get($index));
    };
@endphp

<div class="finals-bracket">
    <div class="finals-path finals-path-left">
        <p class="finals-path-label">Camino 1</p>
        @foreach ($leftRounds as $round)
            <section class="bracket-column bracket-column-{{ $round['key'] }}">
                <header class="bracket-column-head">
                    <h4 class="text-xs uppercase tracking-[0.14em] text-slate-100">{{ $round['label'] }}</h4>
                    <p class="text-[11px] text-slate-400">{{ $splitRoundMatches($round, 'left')->count() }} {{ $splitRoundMatches($round, 'left')->count() === 1 ? 'cruce' : 'cruces' }}</p>
                </header>

                <div class="bracket-column-body">
                    @foreach ($splitRoundMatches($round, 'left') as $matchGame)
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
                                <p class="bracket-date">{{ $matchGame->match_date?->format('d M | H:i') }}</p>
                                <div class="bracket-team-row">
                                    <span class="flex min-w-0 items-center gap-2 truncate text-sm font-semibold"><x-team-flag :team="$matchGame->homeTeam" /> <span class="truncate">{{ $matchGame->homeTeam?->name ?? 'Por definir' }}</span></span>
                                    @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                        <span class="text-sm font-bold text-rose-700">{{ $matchGame->home_score }}</span>
                                    @endif
                                </div>
                                <div class="bracket-team-row">
                                    <span class="flex min-w-0 items-center gap-2 truncate text-sm font-semibold"><x-team-flag :team="$matchGame->awayTeam" /> <span class="truncate">{{ $matchGame->awayTeam?->name ?? 'Por definir' }}</span></span>
                                    @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                        <span class="text-sm font-bold text-rose-700">{{ $matchGame->away_score }}</span>
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
    </div>

    <aside class="finals-center">
        <div class="finals-trophy" aria-hidden="true">
            <div class="finals-trophy-cup"></div>
            <div class="finals-trophy-base"></div>
        </div>
        <p class="text-xs uppercase tracking-[0.22em] text-cyan-200">Copa Mundial</p>
        <h4 class="text-4xl font-black text-cyan-300">2026</h4>

        <div class="finals-card finals-card-gold">
            <p class="text-xs uppercase tracking-[0.16em] text-amber-900/80">Final</p>
            @if ($finalMatch)
                <div class="mt-2 space-y-1 text-sm font-black text-slate-950">
                    <p class="flex items-center justify-center gap-2"><x-team-flag :team="$finalMatch->homeTeam" /> {{ $finalMatch->homeTeam?->name ?? 'Por definir' }}</p>
                    <p class="flex items-center justify-center gap-2"><x-team-flag :team="$finalMatch->awayTeam" /> {{ $finalMatch->awayTeam?->name ?? 'Por definir' }}</p>
                </div>
                @if ($finalMatch->status === 'finished' && $finalMatch->home_score !== null && $finalMatch->away_score !== null)
                    <p class="mt-2 text-lg font-black text-rose-700">{{ $finalMatch->home_score }} - {{ $finalMatch->away_score }}</p>
                @else
                    <p class="mt-2 text-xs font-semibold text-amber-900/70">Pendiente de jugar</p>
                @endif
            @else
                <p class="mt-2 text-sm font-black text-slate-950">Cruce no definido</p>
            @endif
        </div>

        @if ($showThirdPlace)
            <div class="finals-card finals-card-bronze">
                <p class="text-xs uppercase tracking-[0.16em] text-amber-950/80">Tercer puesto</p>
                @if ($thirdPlaceMatch)
                    <p class="mt-2 text-sm font-black text-slate-950">{{ $thirdPlaceMatch->homeTeam?->name }} vs {{ $thirdPlaceMatch->awayTeam?->name }}</p>
                    <p class="mt-1 text-sm font-black text-rose-700">{{ $thirdPlaceMatch->home_score }} - {{ $thirdPlaceMatch->away_score }}</p>
                @else
                    <p class="mt-2 text-sm font-semibold text-amber-950/70">Sin resultado registrado</p>
                @endif
            </div>
        @endif

        <div class="finals-card">
            <p class="text-xs uppercase tracking-[0.16em] text-slate-300">Campeon</p>
            <p class="mt-1 text-xl font-black text-amber-300">{{ $championName ?? 'Por definir' }}</p>
        </div>
    </aside>

    <div class="finals-path finals-path-right">
        <p class="finals-path-label">Camino 2</p>
        @foreach ($rightRounds as $round)
            <section class="bracket-column bracket-column-{{ $round['key'] }}">
                <header class="bracket-column-head">
                    <h4 class="text-xs uppercase tracking-[0.14em] text-slate-100">{{ $round['label'] }}</h4>
                    <p class="text-[11px] text-slate-400">{{ $splitRoundMatches($round, 'right')->count() }} {{ $splitRoundMatches($round, 'right')->count() === 1 ? 'cruce' : 'cruces' }}</p>
                </header>

                <div class="bracket-column-body">
                    @foreach ($splitRoundMatches($round, 'right') as $matchGame)
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
                                <p class="bracket-date">{{ $matchGame->match_date?->format('d M | H:i') }}</p>
                                <div class="bracket-team-row">
                                    <span class="flex min-w-0 items-center gap-2 truncate text-sm font-semibold"><x-team-flag :team="$matchGame->homeTeam" /> <span class="truncate">{{ $matchGame->homeTeam?->name ?? 'Por definir' }}</span></span>
                                    @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                        <span class="text-sm font-bold text-rose-700">{{ $matchGame->home_score }}</span>
                                    @endif
                                </div>
                                <div class="bracket-team-row">
                                    <span class="flex min-w-0 items-center gap-2 truncate text-sm font-semibold"><x-team-flag :team="$matchGame->awayTeam" /> <span class="truncate">{{ $matchGame->awayTeam?->name ?? 'Por definir' }}</span></span>
                                    @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                        <span class="text-sm font-bold text-rose-700">{{ $matchGame->away_score }}</span>
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
    </div>
</div>
