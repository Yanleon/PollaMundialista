<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="section-title">Mis pronosticos</h1>
            <p class="section-subtitle">Registra o actualiza tus marcadores antes del cierre de cada partido.</p>
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
                <div class="grid gap-4 md:grid-cols-2">
                    @foreach ($openMatches as $matchGame)
                        <x-match-card :match-game="$matchGame" :prediction="$openPredictions->get($matchGame->id)" :editable="$matchGame->isPredictionOpen()" />
                    @endforeach
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
                                            <p class="truncate text-sm font-semibold text-slate-100">{{ $matchGame->homeTeam?->name ?? 'Por definir' }}</p>
                                            <x-badge :variant="$statusVariant" class="shrink-0">{{ $matchGame->status }}</x-badge>
                                        </div>

                                        <div class="my-2 flex items-center justify-center">
                                            <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-slate-600 bg-slate-900 text-[11px] font-bold text-rose-200">VS</span>
                                        </div>

                                        <p class="truncate text-sm font-semibold text-slate-100">{{ $matchGame->awayTeam?->name ?? 'Por definir' }}</p>

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

        <section>
            <h2 class="mb-4 text-lg font-semibold text-slate-100">Historial de predicciones</h2>
            <x-table :headers="['Partido', 'Fecha', 'Mi marcador', 'Puntos', 'Estado']">
                @forelse ($predictions as $prediction)
                    <tr class="hover:bg-slate-800/60 transition">
                        <td class="px-4 py-3 text-sm text-slate-100">{{ $prediction->matchGame?->homeTeam?->name }} vs {{ $prediction->matchGame?->awayTeam?->name }}</td>
                        <td class="px-4 py-3 text-sm text-slate-300">{{ $prediction->matchGame?->match_date?->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-slate-100">{{ $prediction->getPredictedResult() }}</td>
                        <td class="px-4 py-3 text-sm font-bold text-rose-300">{{ $prediction->points }}</td>
                        <td class="px-4 py-3">
                            @if ($prediction->matchGame?->status === 'finished')
                                <x-badge variant="success">Finalizado</x-badge>
                            @else
                                <x-badge variant="info">En juego</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-5 text-center text-sm text-slate-400">Aun no has registrado pronosticos.</td>
                    </tr>
                @endforelse
            </x-table>

            <div class="mt-4">
                {{ $predictions->links() }}
            </div>
        </section>
    </div>
</x-app-layout>
