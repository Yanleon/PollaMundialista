<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Pronosticos de todos</h1>
                <p class="section-subtitle">Consulta los marcadores enviados por los demas participantes cuando cada partido ya inicio.</p>
            </div>
            <a href="{{ route('participant.predictions.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-900/75 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-rose-500/50 hover:text-rose-200">Mis pronosticos</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($matchDays->isEmpty())
            <x-card>
                <p class="text-sm text-slate-300">No hay partidos registrados para consultar.</p>
            </x-card>
        @else
            @php
                $initialDay = $matchDays->firstWhere(fn (array $day): bool => (bool) $day['date']?->isToday()) ?? $matchDays->first();
            @endphp

            <div x-data="{ selectedDay: '{{ $initialDay['anchor'] }}' }" x-init="if (window.location.hash && window.location.hash.length > 1) selectedDay = window.location.hash.slice(1)">
                <div class="-mx-4 mb-6 overflow-x-auto border-y border-slate-800 bg-slate-950/95 px-4 py-3 sm:mx-0 sm:rounded-2xl sm:border sm:bg-slate-950/80">
                    <div class="flex min-w-max gap-2">
                        @foreach ($matchDays as $day)
                            @php
                                $isToday = $day['date']?->isToday();
                                $isTomorrow = $day['date']?->isTomorrow();
                                $label = $day['date'] ? ($isToday ? 'Hoy' : ($isTomorrow ? 'Manana' : $day['date']->locale('es')->isoFormat('dddd, D MMM'))) : 'Sin fecha';
                                $allStarted = $day['locked_matches'] === 0;
                            @endphp

                            <button type="button" x-on:click="selectedDay = '{{ $day['anchor'] }}'; history.replaceState(null, '', '#{{ $day['anchor'] }}')" class="group flex min-w-36 flex-col rounded-2xl border px-4 py-3 text-left transition" x-bind:class="selectedDay === '{{ $day['anchor'] }}' ? 'border-white bg-white text-slate-950 shadow-[0_0_24px_rgba(255,255,255,0.18)]' : 'border-slate-800 bg-slate-900/85 text-slate-200 hover:border-rose-500/60 hover:bg-slate-900'">
                                <span class="text-sm font-bold capitalize leading-tight">{{ $label }}</span>
                                <span class="mt-1 text-xs" x-bind:class="selectedDay === '{{ $day['anchor'] }}' ? 'text-slate-600' : 'text-slate-400 group-hover:text-slate-300'">{{ $day['matches']->count() }} {{ $day['matches']->count() === 1 ? 'partido' : 'partidos' }}</span>
                                <span class="mt-2 h-1 rounded-full {{ $allStarted ? 'bg-emerald-400' : 'bg-rose-500/70' }}"></span>
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-8">
                    @foreach ($matchDays as $day)
                        @php
                            $heading = $day['date'] ? $day['date']->locale('es')->isoFormat('dddd, D [de] MMMM') : 'Sin fecha';
                        @endphp

                        <section id="{{ $day['anchor'] }}" x-show="selectedDay === '{{ $day['anchor'] }}'" class="space-y-4">
                            <div class="flex flex-col gap-2 border-b border-slate-800 pb-3 sm:flex-row sm:items-end sm:justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-rose-300">Fecha de partido</p>
                                    <h2 class="mt-1 text-2xl font-black capitalize text-slate-100">{{ $heading }}</h2>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <x-badge variant="info">{{ $day['matches']->count() }} {{ $day['matches']->count() === 1 ? 'partido' : 'partidos' }}</x-badge>
                                    <x-badge variant="success">{{ $day['started_matches'] }} visibles</x-badge>
                                    @if ($day['locked_matches'] > 0)
                                        <x-badge variant="warning">{{ $day['locked_matches'] }} bloqueados</x-badge>
                                    @endif
                                </div>
                            </div>

                            <div class="grid gap-4 xl:grid-cols-2">
                                @foreach ($day['matches'] as $matchGame)
                                    @php
                                        $canShowPredictions = $matchGame->match_date?->lte(now()) ?? false;
                                        $isFinished = $matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null;
                                        $predictions = $matchGame->predictions->sortBy(fn ($prediction) => $prediction->user?->name)->values();
                                    @endphp

                                    <article class="overflow-hidden rounded-3xl border border-slate-800 bg-slate-950/70 shadow-[0_22px_60px_rgba(2,6,23,0.32)]">
                                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-800 bg-slate-900/65 px-4 py-3">
                                            <div>
                                                <p class="text-sm font-bold text-slate-100">{{ $matchGame->homeTeam?->name ?? 'Equipo local' }} vs {{ $matchGame->awayTeam?->name ?? 'Equipo visitante' }}</p>
                                                <p class="mt-1 text-xs text-slate-400">{{ $matchGame->phase }} · {{ $matchGame->match_date?->format('d/m/Y H:i') ?? 'Sin fecha' }}</p>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if ($isFinished)
                                                    <span class="rounded-full bg-rose-500/15 px-3 py-1 text-xs font-bold text-rose-200 ring-1 ring-inset ring-rose-400/30">Final: {{ $matchGame->getFinalResult() }}</span>
                                                @endif
                                                @if ($canShowPredictions)
                                                    <x-badge variant="success">Disponible</x-badge>
                                                @else
                                                    <x-badge variant="warning">Visible {{ $matchGame->match_date?->format('H:i') ?? 'al iniciar' }}</x-badge>
                                                @endif
                                            </div>
                                        </div>

                                        @if (! $canShowPredictions)
                                            <div class="p-5">
                                                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 px-4 py-5 text-center">
                                                    <p class="text-sm font-semibold text-slate-100">Pronosticos bloqueados</p>
                                                    <p class="mt-1 text-sm text-slate-400">Se podran ver cuando el partido inicie.</p>
                                                </div>
                                            </div>
                                        @elseif ($predictions->isEmpty())
                                            <div class="p-5">
                                                <p class="rounded-2xl border border-slate-800 bg-slate-900/70 px-4 py-4 text-center text-sm text-slate-400">Nadie ha enviado pronostico para este partido.</p>
                                            </div>
                                        @else
                                            <div class="divide-y divide-slate-800">
                                                @foreach ($predictions as $prediction)
                                                    @php
                                                        $predictedHome = (int) $prediction->predicted_home_score;
                                                        $predictedAway = (int) $prediction->predicted_away_score;
                                                        $actualHome = $isFinished ? (int) $matchGame->home_score : null;
                                                        $actualAway = $isFinished ? (int) $matchGame->away_score : null;
                                                        $homeGoalPoint = $isFinished && ! $prediction->is_exact_score && $predictedHome === $actualHome;
                                                        $awayGoalPoint = $isFinished && ! $prediction->is_exact_score && $predictedAway === $actualAway;
                                                        $isCurrentUser = $prediction->user_id === auth()->id();
                                                    @endphp

                                                    <div class="flex items-center justify-between gap-3 px-4 py-3">
                                                        <div class="min-w-0">
                                                            <p class="truncate text-sm font-semibold text-slate-100">
                                                                {{ $prediction->user?->name ?? 'Participante' }}
                                                                @if ($isCurrentUser)
                                                                    <span class="ml-2 rounded-full bg-cyan-300/10 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-cyan-100 ring-1 ring-inset ring-cyan-300/30">Tu</span>
                                                                @endif
                                                            </p>
                                                            @if ($prediction->user?->department)
                                                                <p class="truncate text-xs text-slate-500">{{ $prediction->user->department }}</p>
                                                            @endif
                                                        </div>
                                                        <div class="shrink-0 text-right">
                                                            <span class="inline-flex rounded-full bg-rose-500/15 px-3 py-1 text-sm font-black text-rose-200 ring-1 ring-inset ring-rose-400/30">{{ $prediction->getPredictedResult() }}</span>
                                                            @if ($isFinished)
                                                                <p class="mt-1 text-xs font-bold text-emerald-200">{{ $prediction->points }} pts</p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if ($isFinished)
                                                        <div class="flex flex-wrap gap-2 px-4 pb-3">
                                                            @if ($prediction->is_exact_score)
                                                                <x-badge variant="success">Marcador exacto: +5</x-badge>
                                                            @else
                                                                <x-badge :variant="$prediction->is_correct_result ? 'success' : 'muted'">Resultado: {{ $prediction->is_correct_result ? '+3' : '+0' }}</x-badge>
                                                                <x-badge :variant="$homeGoalPoint ? 'warning' : 'muted'">Gol local exacto: {{ $homeGoalPoint ? '+1' : '+0' }}</x-badge>
                                                                <x-badge :variant="$awayGoalPoint ? 'warning' : 'muted'">Gol visitante exacto: {{ $awayGoalPoint ? '+1' : '+0' }}</x-badge>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
