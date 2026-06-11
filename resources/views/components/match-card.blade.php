@props([
    'matchGame',
    'prediction' => null,
    'editable' => true,
    'action' => null,
])

@php
    $statusValue = data_get($matchGame, 'status', 'scheduled');

    $statusVariant = match($statusValue) {
        'scheduled' => 'info',
        'live' => 'warning',
        'finished' => 'success',
        'cancelled' => 'danger',
        default => 'muted',
    };

    $statusLabel = match($statusValue) {
        'scheduled' => 'Programado',
        'live' => 'En juego',
        'finished' => 'Finalizado',
        'cancelled' => 'Cancelado',
        default => ucfirst((string) $statusValue),
    };

    $homePrediction = data_get($prediction, 'predicted_home_score');
    $awayPrediction = data_get($prediction, 'predicted_away_score');

    $homeTeamName = data_get($matchGame, 'homeTeam.name') ?? data_get($matchGame, 'home_team_name') ?? 'Equipo local';
    $awayTeamName = data_get($matchGame, 'awayTeam.name') ?? data_get($matchGame, 'away_team_name') ?? 'Equipo visitante';
    $phase = data_get($matchGame, 'phase', 'Partido');
    $groupName = data_get($matchGame, 'group_name');
    $matchDate = data_get($matchGame, 'match_date');
    $deadline = data_get($matchGame, 'prediction_deadline');

    $matchDateLabel = $matchDate instanceof \Carbon\CarbonInterface ? $matchDate->format('d/m/Y H:i') : (string) $matchDate;
    $deadlineLabel = $deadline instanceof \Carbon\CarbonInterface ? $deadline->format('d/m/Y H:i') : (string) $deadline;

    $actionUrl = $action;

    if (! $actionUrl && $matchGame instanceof \App\Models\MatchGame) {
        $actionUrl = route('participant.predictions.upsert', $matchGame);
    }
@endphp

<x-card class="h-full" :title="$phase" :subtitle="$matchDateLabel">
    <div class="mb-4 flex items-start justify-between gap-3">
        <div>
            <p class="text-sm text-slate-300">{{ $groupName ? 'Grupo '.$groupName : 'Fase eliminatoria' }}</p>
            <p class="mt-1 text-sm text-slate-400">Cierre de pronostico: {{ $deadlineLabel }}</p>
        </div>
        <x-badge :variant="$statusVariant">{{ $statusLabel }}</x-badge>
    </div>

    <div class="mb-4 grid grid-cols-[1fr_auto_1fr] items-center gap-2 rounded-2xl border border-slate-700/70 bg-slate-900/75 p-3 text-sm">
        <div class="truncate font-semibold text-slate-100">{{ $homeTeamName }}</div>
        <div class="text-lg font-black text-rose-300">vs</div>
        <div class="truncate text-right font-semibold text-slate-100">{{ $awayTeamName }}</div>
    </div>

    @if ($editable)
        <form method="POST" action="{{ $actionUrl ?? '#' }}" class="space-y-3">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-300">Local</label>
                    <input type="number" min="0" max="30" name="predicted_home_score" value="{{ old('predicted_home_score', $homePrediction) }}" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100 focus:border-rose-500 focus:outline-none">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-300">Visitante</label>
                    <input type="number" min="0" max="30" name="predicted_away_score" value="{{ old('predicted_away_score', $awayPrediction) }}" class="w-full rounded-xl border border-slate-600 bg-slate-900 px-3 py-2 text-slate-100 focus:border-rose-500 focus:outline-none">
                </div>
            </div>
            <x-button type="submit" class="w-full">Guardar pronostico</x-button>
        </form>
    @else
        <div class="rounded-lg border border-slate-700 bg-slate-900/70 px-3 py-3 text-sm text-slate-300">
            @if ($prediction)
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-400">Tu pronostico registrado</p>
                <div class="grid grid-cols-[1fr_auto_1fr] items-center gap-3 rounded-xl border border-slate-700/70 bg-slate-950/70 px-3 py-2">
                    <div>
                        <p class="truncate text-xs text-slate-400">{{ $homeTeamName }}</p>
                        <p class="text-2xl font-black text-slate-100">{{ $homePrediction }}</p>
                    </div>
                    <div class="text-sm font-bold text-rose-300">vs</div>
                    <div class="text-right">
                        <p class="truncate text-xs text-slate-400">{{ $awayTeamName }}</p>
                        <p class="text-2xl font-black text-slate-100">{{ $awayPrediction }}</p>
                    </div>
                </div>
                <p class="mt-2 text-xs text-slate-500">El tiempo de edicion ya cerro para este partido.</p>
            @else
                Pronostico cerrado para este partido. No registraste marcador antes del cierre.
            @endif
        </div>
    @endif
</x-card>
