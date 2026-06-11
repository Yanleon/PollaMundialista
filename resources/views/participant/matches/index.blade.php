<x-app-layout>
    @php
        $matches = $matches ?? collect([
            (object) ['phase' => 'Fase de grupos', 'group_name' => 'A', 'home_team_name' => 'Colombia', 'away_team_name' => 'Brasil', 'match_date' => '21/06/2026 18:00', 'prediction_deadline' => '21/06/2026 17:50', 'status' => 'scheduled'],
            (object) ['phase' => 'Fase de grupos', 'group_name' => 'B', 'home_team_name' => 'Argentina', 'away_team_name' => 'Uruguay', 'match_date' => '22/06/2026 20:00', 'prediction_deadline' => '22/06/2026 19:50', 'status' => 'live'],
            (object) ['phase' => 'Octavos', 'group_name' => null, 'home_team_name' => 'Mexico', 'away_team_name' => 'USA', 'match_date' => '01/07/2026 19:00', 'prediction_deadline' => '01/07/2026 18:45', 'status' => 'finished'],
        ]);
    @endphp

    <x-slot name="header">
        <div>
            <h1 class="section-title">Partidos del torneo</h1>
            <p class="section-subtitle">Consulta el calendario y estado de cada encuentro.</p>
        </div>
    </x-slot>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ($matches as $matchGame)
            <x-match-card :match-game="$matchGame" :editable="false" />
        @endforeach
    </div>
</x-app-layout>
