<x-app-layout>
    @php
        $matchGame = $matchGame ?? (object) [
            'id' => 1,
            'phase' => 'Fase de grupos',
            'group_name' => 'A',
            'home_team_name' => 'Colombia',
            'away_team_name' => 'Brasil',
            'match_date' => '21/06/2026 18:00',
            'prediction_deadline' => '21/06/2026 17:50',
            'status' => 'scheduled',
        ];

        $prediction = $prediction ?? (object) [
            'predicted_home_score' => 2,
            'predicted_away_score' => 1,
        ];
    @endphp

    <x-slot name="header">
        <div>
            <h1 class="section-title">Formulario de prediccion</h1>
            <p class="section-subtitle">Ingresa tu marcador antes del cierre del partido.</p>
        </div>
    </x-slot>

    <div class="mx-auto max-w-2xl">
        <x-match-card :match-game="$matchGame" :prediction="$prediction" action="#" />
    </div>
</x-app-layout>
