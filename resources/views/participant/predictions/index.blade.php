<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Mis pronosticos</h1>
                <p class="section-subtitle">Registra o actualiza tus marcadores antes del cierre de cada partido.</p>
            </div>
            <a href="{{ route('participant.predictions.history') }}" class="inline-flex items-center justify-center rounded-full border border-cyan-300/50 bg-cyan-300/10 px-4 py-2 text-sm font-semibold text-cyan-100 transition hover:border-cyan-200 hover:bg-cyan-300/15">Ver historial</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-xl border border-green-500/40 bg-green-600/10 px-4 py-3 text-sm text-green-200">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-500/40 bg-red-600/10 px-4 py-3 text-sm text-red-200">{{ session('error') }}</div>
        @endif

        <section>
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-slate-100">Formulario de prediccion</h2>
                <x-badge variant="warning">Abiertos: {{ $openMatchesCount ?? 0 }} | Ventana: proximos 4 dias</x-badge>
            </div>

            @if ($openMatches->isEmpty())
                <x-card>
                    <p class="text-sm text-slate-300">No hay partidos abiertos para enviar predicciones.</p>
                </x-card>
            @else
                <p class="mb-3 text-sm text-slate-300">Si un partido no permite edicion, revisa su fecha limite de pronostico.</p>

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
                                    $allPredicted = $day['pending_matches'] === 0;
                                @endphp

                                <button type="button" x-on:click="selectedDay = '{{ $day['anchor'] }}'; history.replaceState(null, '', '#{{ $day['anchor'] }}')" class="group flex min-w-36 flex-col rounded-2xl border px-4 py-3 text-left transition" x-bind:class="selectedDay === '{{ $day['anchor'] }}' ? 'border-white bg-white text-slate-950 shadow-[0_0_24px_rgba(255,255,255,0.18)]' : 'border-slate-800 bg-slate-900/85 text-slate-200 hover:border-rose-500/60 hover:bg-slate-900'">
                                    <span class="text-sm font-bold capitalize leading-tight">{{ $label }}</span>
                                    <span class="mt-1 text-xs" x-bind:class="selectedDay === '{{ $day['anchor'] }}' ? 'text-slate-600' : 'text-slate-400 group-hover:text-slate-300'">{{ $day['matches']->count() }} {{ $day['matches']->count() === 1 ? 'partido' : 'partidos' }}</span>
                                    <span class="mt-2 h-1 rounded-full {{ $allPredicted ? 'bg-emerald-400' : 'bg-rose-500/70' }}"></span>
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
                                        <h3 class="mt-1 text-2xl font-black capitalize text-slate-100">{{ $heading }}</h3>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        <x-badge variant="info">{{ $day['matches']->count() }} {{ $day['matches']->count() === 1 ? 'partido' : 'partidos' }}</x-badge>
                                        <x-badge variant="success">{{ $day['predicted_matches'] }} pronosticados</x-badge>
                                        @if ($day['pending_matches'] > 0)
                                            <x-badge variant="warning">{{ $day['pending_matches'] }} pendientes</x-badge>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    @foreach ($day['matches'] as $matchGame)
                                        <x-match-card :match-game="$matchGame" :prediction="$openPredictions->get($matchGame->id)" :editable="$matchGame->isPredictionOpen()" :return-anchor="$day['anchor']" />
                                    @endforeach
                                </div>
                            </section>
                        @endforeach
                    </div>
                </div>
            @endif
        </section>

        <section>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-100">Llaves de eliminacion</h2>
                <p class="text-sm text-slate-300">Vista de octavos, cuartos, semifinal y final.</p>
            </div>

            <div class="knockout-grid">
                @foreach ($bracketRounds as $round)
                    <x-card class="h-full">
                        <div class="mb-3 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-slate-100">{{ $round['label'] }}</h3>
                            <x-badge variant="muted">{{ $round['matches']->count() }} cruces</x-badge>
                        </div>

                        <div class="space-y-3">
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

                                    <article class="knockout-match">
                                        <div class="flex items-center justify-between gap-2">
                                            <p class="flex min-w-0 items-center gap-2 truncate text-sm font-semibold text-slate-100"><x-team-flag :team="$matchGame->homeTeam" /> <span class="truncate">{{ $matchGame->homeTeam?->name ?? 'Por definir' }}</span></p>
                                            <x-badge :variant="$statusVariant" class="shrink-0">{{ $matchGame->status }}</x-badge>
                                        </div>

                                        <div class="my-2 flex items-center justify-center">
                                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-slate-600 bg-slate-900 text-[11px] font-bold text-rose-200">VS</span>
                                        </div>

                                        <p class="flex items-center gap-2 truncate text-sm font-semibold text-slate-100"><x-team-flag :team="$matchGame->awayTeam" /> <span class="truncate">{{ $matchGame->awayTeam?->name ?? 'Por definir' }}</span></p>

                                        <div class="mt-2 flex items-center justify-between text-xs text-slate-400">
                                            <span>{{ $matchGame->match_date?->format('d/m H:i') }}</span>
                                            @if ($matchGame->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null)
                                                <span class="font-semibold text-rose-300">{{ $matchGame->home_score }} - {{ $matchGame->away_score }}</span>
                                            @endif
                                        </div>
                                    </article>
                                @else
                                    <article class="knockout-match opacity-70">
                                        <p class="text-center text-sm text-slate-400">Cruce pendiente de definir</p>
                                    </article>
                                @endif
                            @endforeach
                        </div>
                    </x-card>
                @endforeach
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h2 class="text-lg font-semibold text-slate-100">Finalizados y goles</h2>
                <p class="text-sm text-slate-300">Resultados oficiales de los cruces de eliminacion.</p>
            </div>

            <x-table :headers="['Partido', 'Fase', 'Fecha', 'Estado', 'Goles']">
                @forelse ($finishedBracketMatches as $finishedMatch)
                    <tr class="transition hover:bg-slate-800/60">
                        <td class="px-4 py-3 text-sm font-semibold text-slate-100">{{ $finishedMatch->homeTeam?->name }} vs {{ $finishedMatch->awayTeam?->name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $finishedMatch->phase }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $finishedMatch->match_date?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3"><x-badge variant="success">Finalizado</x-badge></td>
                        <td class="px-4 py-3 text-sm font-bold text-rose-300">{{ $finishedMatch->home_score }} - {{ $finishedMatch->away_score }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-5 text-center text-sm text-slate-400">Aun no hay cruces finalizados con marcador registrado.</td>
                    </tr>
                @endforelse
            </x-table>
        </section>

        <x-card>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-slate-100">Historial de predicciones</h2>
                    <p class="text-sm text-slate-300">Consulta tus marcadores enviados y el detalle de puntos ganados por partido.</p>
                </div>
                <a href="{{ route('participant.predictions.history') }}" class="inline-flex items-center justify-center rounded-full bg-gradient-to-r from-white via-white to-rose-500 px-4 py-2 text-sm font-semibold text-slate-950 transition hover:scale-[1.02]">Abrir historial</a>
            </div>
        </x-card>
    </div>
</x-app-layout>
