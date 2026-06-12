<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="section-title">Historial de predicciones</h1>
                <p class="section-subtitle">Revisa tus marcadores enviados y de donde salen tus puntos.</p>
            </div>
            <a href="{{ route('participant.predictions.index') }}" class="inline-flex items-center justify-center rounded-full border border-slate-700 bg-slate-900/75 px-4 py-2 text-sm font-semibold text-slate-100 transition hover:border-rose-500/50 hover:text-rose-200">Volver a pronosticos</a>
        </div>
    </x-slot>

    <div class="space-y-6">
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Pronosticos enviados</p>
                <p class="mt-2 text-3xl font-black text-slate-100">{{ $totalPredictions }}</p>
            </x-card>

            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Puntos por partidos</p>
                <p class="mt-2 text-3xl font-black text-rose-300">{{ $totalPoints }}</p>
            </x-card>

            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Marcadores exactos</p>
                <p class="mt-2 text-3xl font-black text-amber-300">{{ $exactScores }}</p>
            </x-card>

            <x-card>
                <p class="text-xs uppercase tracking-wide text-slate-400">Resultados acertados</p>
                <p class="mt-2 text-3xl font-black text-emerald-300">{{ $correctResults }}</p>
            </x-card>
        </section>

        <section class="overflow-hidden rounded-[2rem] border border-slate-700 bg-slate-950/70 shadow-[0_24px_60px_rgba(0,0,0,0.34)]">
            <div class="border-b border-slate-800 bg-gradient-to-r from-slate-900 via-slate-900 to-rose-950/50 px-5 py-4">
                <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-100">Detalle por partido</h2>
                        <p class="text-sm text-slate-400">Los puntos solo se asignan cuando el partido tiene marcador oficial registrado.</p>
                    </div>
                    <x-badge variant="info">5 exacto · 3 resultado · +1 goles</x-badge>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-800">
                    <thead class="bg-slate-950/95">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Partido</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Marcadores</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Puntos</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Detalle</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-400">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        @forelse ($predictions as $prediction)
                            @php
                                $matchGame = $prediction->matchGame;
                                $isFinished = $matchGame?->status === 'finished' && $matchGame->home_score !== null && $matchGame->away_score !== null;
                                $predictedHome = (int) $prediction->predicted_home_score;
                                $predictedAway = (int) $prediction->predicted_away_score;
                                $actualHome = $isFinished ? (int) $matchGame->home_score : null;
                                $actualAway = $isFinished ? (int) $matchGame->away_score : null;
                                $homeGoalPoint = $isFinished && ! $prediction->is_exact_score && $predictedHome === $actualHome;
                                $awayGoalPoint = $isFinished && ! $prediction->is_exact_score && $predictedAway === $actualAway;
                            @endphp

                            <tr class="transition hover:bg-slate-900/80">
                                <td class="px-4 py-4 align-top">
                                    <p class="flex min-w-[220px] items-center gap-2 text-sm font-semibold text-slate-100">
                                        <x-team-flag :team="$matchGame?->homeTeam" />
                                        <span>{{ $matchGame?->homeTeam?->name ?? 'Por definir' }}</span>
                                    </p>
                                    <p class="mt-1 flex min-w-[220px] items-center gap-2 text-sm font-semibold text-slate-100">
                                        <x-team-flag :team="$matchGame?->awayTeam" />
                                        <span>{{ $matchGame?->awayTeam?->name ?? 'Por definir' }}</span>
                                    </p>
                                    <p class="mt-2 text-xs text-slate-400">{{ $matchGame?->phase }} · {{ $matchGame?->match_date?->format('d/m/Y H:i') }}</p>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="grid min-w-[180px] gap-2">
                                        <div class="rounded-2xl border border-cyan-300/25 bg-cyan-300/10 px-3 py-2">
                                            <p class="text-xs text-cyan-100/80">Mi pronostico</p>
                                            <p class="text-lg font-black text-cyan-100">{{ $prediction->getPredictedResult() }}</p>
                                        </div>
                                        <div class="rounded-2xl border border-slate-700 bg-slate-900/80 px-3 py-2">
                                            <p class="text-xs text-slate-400">Marcador oficial</p>
                                            <p class="text-lg font-black {{ $isFinished ? 'text-slate-100' : 'text-slate-500' }}">{{ $isFinished ? $matchGame->getFinalResult() : 'Pendiente' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-rose-400/35 bg-rose-500/15 text-2xl font-black text-rose-100 shadow-[0_0_24px_rgba(244,63,94,0.12)]">
                                        {{ $prediction->points }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="flex min-w-[260px] flex-wrap gap-2">
                                        @if (! $isFinished)
                                            <x-badge variant="muted">Pendiente de resultado</x-badge>
                                        @elseif ($prediction->is_exact_score)
                                            <x-badge variant="success">Marcador exacto: +5</x-badge>
                                        @else
                                            <x-badge :variant="$prediction->is_correct_result ? 'success' : 'muted'">Resultado: {{ $prediction->is_correct_result ? '+3' : '+0' }}</x-badge>
                                            <x-badge :variant="$homeGoalPoint ? 'warning' : 'muted'">Gol local exacto: {{ $homeGoalPoint ? '+1' : '+0' }}</x-badge>
                                            <x-badge :variant="$awayGoalPoint ? 'warning' : 'muted'">Gol visitante exacto: {{ $awayGoalPoint ? '+1' : '+0' }}</x-badge>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    @if ($isFinished)
                                        <x-badge variant="success">Finalizado</x-badge>
                                    @elseif ($matchGame?->isPredictionOpen())
                                        <x-badge variant="warning">Editable</x-badge>
                                    @else
                                        <x-badge variant="info">Cerrado</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-slate-400">Aun no has registrado pronosticos.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div>
            {{ $predictions->links() }}
        </div>
    </div>
</x-app-layout>
