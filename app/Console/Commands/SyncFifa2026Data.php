<?php

namespace App\Console\Commands;

use App\Models\MatchGame;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

#[Signature('app:sync-fifa2026-data')]
#[Description('Importa equipos, grupos y partidos iniciales desde FIFA 2026')]
class SyncFifa2026Data extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Consultando feed oficial FIFA 2026...');

        $response = Http::timeout(60)
            ->acceptJson()
            ->withHeaders(['User-Agent' => 'Laravel Polla Mundialista'])
            ->withOptions(['verify' => false])
            ->get('https://api.fifa.com/api/v3/calendar/matches', [
                'idCompetition' => 17,
                'idSeason' => 285023,
                'count' => 500,
            ]);

        if (! $response->successful()) {
            $this->error('No fue posible consultar FIFA API.');

            return self::FAILURE;
        }

        $results = $response->json('Results', []);

        if (! is_array($results) || $results === []) {
            $this->error('FIFA API no devolvio partidos para la temporada 2026.');

            return self::FAILURE;
        }

        $matches = collect($results)
            ->filter(function (array $match): bool {
                $home = $match['Home'] ?? null;
                $away = $match['Away'] ?? null;

                return is_array($home)
                    && is_array($away)
                    && ! empty($home['IdTeam'])
                    && ! empty($away['IdTeam'])
                    && ! empty($home['Abbreviation'])
                    && ! empty($away['Abbreviation'])
                    && ! empty($match['Date']);
            })
            ->values();

        $teamMap = [];

        foreach ($matches as $match) {
            $group = $this->normalizeGroupName($match['GroupName'] ?? []);

            foreach (['Home', 'Away'] as $side) {
                $team = $match[$side];
                $code = strtoupper((string) $team['Abbreviation']);

                if ($code === '') {
                    continue;
                }

                $teamMap[$code] = [
                    'name' => $this->localizedText($team['TeamName'] ?? []),
                    'code' => $code,
                    'flag_url' => $this->normalizeFlagUrl((string) ($team['PictureUrl'] ?? '')),
                    'group_name' => $group,
                    'status' => 'active',
                ];
            }
        }

        $teamStats = ['created' => 0, 'updated' => 0];
        $matchStats = ['created' => 0, 'updated' => 0];

        DB::transaction(function () use ($teamMap, $matches, &$teamStats, &$matchStats): void {
            foreach ($teamMap as $teamData) {
                $team = Team::firstOrNew(['code' => $teamData['code']]);
                $isNew = ! $team->exists;

                $team->fill($teamData);
                $team->save();

                $teamStats[$isNew ? 'created' : 'updated']++;
            }

            foreach ($matches as $match) {
                $homeCode = strtoupper((string) $match['Home']['Abbreviation']);
                $awayCode = strtoupper((string) $match['Away']['Abbreviation']);

                $homeTeam = Team::where('code', $homeCode)->first();
                $awayTeam = Team::where('code', $awayCode)->first();

                if (! $homeTeam || ! $awayTeam) {
                    continue;
                }

                $matchDate = Carbon::parse($match['Date'])->setTimezone(config('app.timezone'));
                $homeScore = $match['Home']['Score'] ?? null;
                $awayScore = $match['Away']['Score'] ?? null;

                $row = MatchGame::firstOrNew([
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'match_date' => $matchDate,
                ]);

                $isNew = ! $row->exists;

                $row->fill([
                    'phase' => $this->localizedText($match['StageName'] ?? []) ?: 'Fase de grupos',
                    'group_name' => $this->normalizeGroupName($match['GroupName'] ?? []),
                    'home_score' => is_numeric($homeScore) ? (int) $homeScore : null,
                    'away_score' => is_numeric($awayScore) ? (int) $awayScore : null,
                    'status' => is_numeric($homeScore) && is_numeric($awayScore) ? 'finished' : 'scheduled',
                    'prediction_deadline' => (clone $matchDate)->subMinutes(10),
                ]);

                $row->save();

                $matchStats[$isNew ? 'created' : 'updated']++;
            }
        });

        $this->info('Sincronizacion completada.');
        $this->line("Equipos - creados: {$teamStats['created']}, actualizados: {$teamStats['updated']}");
        $this->line("Partidos - creados: {$matchStats['created']}, actualizados: {$matchStats['updated']}");

        return self::SUCCESS;
    }

    private function localizedText(array $translations): string
    {
        if ($translations === []) {
            return '';
        }

        foreach ($translations as $item) {
            $locale = strtolower((string) ($item['Locale'] ?? ''));

            if (in_array($locale, ['es-es', 'es', 'en-gb', 'en'], true)) {
                return (string) ($item['Description'] ?? '');
            }
        }

        return (string) ($translations[0]['Description'] ?? '');
    }

    private function normalizeGroupName(array $group): ?string
    {
        $text = $this->localizedText($group);

        if ($text === '') {
            return null;
        }

        return trim(str_ireplace('Group', '', $text));
    }

    private function normalizeFlagUrl(string $url): ?string
    {
        if ($url === '') {
            return null;
        }

        return str_replace(['{format}', '{size}'], ['4', '3'], $url);
    }
}
