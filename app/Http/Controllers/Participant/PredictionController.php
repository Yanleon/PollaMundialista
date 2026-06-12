<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Participant\UpsertPredictionRequest;
use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PredictionController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $windowStart = now()->startOfDay();
        $windowEnd = now()->addDays(4)->endOfDay();

        $candidateMatches = MatchGame::query()
            ->whereIn('status', ['scheduled', 'live'])
            ->whereBetween('match_date', [$windowStart, $windowEnd])
            ->with(['homeTeam', 'awayTeam'])
            ->orderByRaw('CASE WHEN DATE(match_date) = ? THEN 0 ELSE 1 END', [now()->toDateString()])
            ->orderBy('match_date')
            ->get();

        if ($candidateMatches->isEmpty()) {
            $candidateMatches = MatchGame::query()
                ->whereIn('status', ['scheduled', 'live'])
                ->with(['homeTeam', 'awayTeam'])
                ->orderBy('match_date')
                ->limit(20)
                ->get();
        }

        $openPredictions = Prediction::query()
            ->where('user_id', $user->id)
            ->whereIn('match_game_id', $candidateMatches->pluck('id'))
            ->get()
            ->keyBy('match_game_id');

        $openMatchesCount = $candidateMatches->filter(fn (MatchGame $matchGame) => $matchGame->isPredictionOpen())->count();

        $allMatches = MatchGame::query()
            ->with(['homeTeam', 'awayTeam'])
            ->orderBy('match_date')
            ->get();

        $bracketRounds = $this->buildBracketRounds($allMatches);

        $finishedBracketMatches = $allMatches
            ->filter(fn (MatchGame $matchGame) => $this->detectBracketRound($matchGame->phase) !== null)
            ->where('status', 'finished')
            ->filter(fn (MatchGame $matchGame) => $matchGame->home_score !== null && $matchGame->away_score !== null)
            ->sortByDesc('match_date')
            ->values();

        return view('participant.predictions.index', [
            'openMatches' => $candidateMatches,
            'openMatchesCount' => $openMatchesCount,
            'openPredictions' => $openPredictions,
            'bracketRounds' => $bracketRounds,
            'finishedBracketMatches' => $finishedBracketMatches,
        ]);
    }

    public function history(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $finishedPredictionsQuery = Prediction::query()
            ->where('user_id', $user->id)
            ->whereHas('matchGame', function ($query): void {
                $query->where('status', 'finished')
                    ->whereNotNull('home_score')
                    ->whereNotNull('away_score');
            });

        $predictions = (clone $finishedPredictionsQuery)
            ->with(['matchGame.homeTeam', 'matchGame.awayTeam'])
            ->latest()
            ->paginate(20);

        $totalPredictions = (clone $finishedPredictionsQuery)->count();

        $totalPoints = (int) (clone $finishedPredictionsQuery)->sum('points');

        $exactScores = (clone $finishedPredictionsQuery)
            ->where('is_exact_score', true)
            ->count();

        $correctResults = (clone $finishedPredictionsQuery)
            ->where('is_correct_result', true)
            ->count();

        return view('participant.predictions.history', [
            'predictions' => $predictions,
            'totalPredictions' => $totalPredictions,
            'totalPoints' => $totalPoints,
            'exactScores' => $exactScores,
            'correctResults' => $correctResults,
        ]);
    }

    private function buildBracketRounds(Collection $matches): Collection
    {
        return collect($this->bracketRoundConfig())
            ->map(function (array $round) use ($matches): array {
                $roundMatches = $matches
                    ->filter(fn (MatchGame $matchGame) => $this->detectBracketRound($matchGame->phase) === $round['key'])
                    ->values();

                $filledSlots = collect(range(0, $round['slots'] - 1))
                    ->map(fn (int $index) => $roundMatches->get($index));

                return [
                    'key' => $round['key'],
                    'label' => $round['label'],
                    'matches' => $filledSlots,
                ];
            })
            ->values();
    }

    private function detectBracketRound(?string $phase): ?string
    {
        if (! $phase) {
            return null;
        }

        $normalized = Str::lower($phase);

        foreach ($this->bracketRoundConfig() as $round) {
            foreach ($round['keywords'] as $keyword) {
                if (str_contains($normalized, $keyword)) {
                    return $round['key'];
                }
            }
        }

        return null;
    }

    private function bracketRoundConfig(): array
    {
        return [
            [
                'key' => 'round_of_16',
                'label' => 'Octavos',
                'slots' => 8,
                'keywords' => ['octavos', 'octavo', 'round of 16', 'round_of_16', 'r16', '1/8'],
            ],
            [
                'key' => 'quarterfinals',
                'label' => 'Cuartos',
                'slots' => 4,
                'keywords' => ['cuartos', 'cuarto', 'quarterfinal', 'quarter-final', 'quarter final', '1/4'],
            ],
            [
                'key' => 'semifinals',
                'label' => 'Semifinal',
                'slots' => 2,
                'keywords' => ['semifinal', 'semi-final', 'semi final', 'semi'],
            ],
            [
                'key' => 'final',
                'label' => 'Final',
                'slots' => 1,
                'keywords' => ['final'],
            ],
        ];
    }

    public function upsert(UpsertPredictionRequest $request, MatchGame $matchGame): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $matchGame->isPredictionOpen()) {
            return back()->with('error', 'No puedes modificar este pronostico porque el partido ya cerro.');
        }

        Prediction::updateOrCreate(
            [
                'user_id' => $user->id,
                'match_game_id' => $matchGame->id,
            ],
            [
                'predicted_home_score' => $request->integer('predicted_home_score'),
                'predicted_away_score' => $request->integer('predicted_away_score'),
            ],
        );

        return back()->with('success', 'Pronostico guardado correctamente.');
    }
}
