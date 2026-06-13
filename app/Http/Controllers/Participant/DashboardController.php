<?php

namespace App\Http\Controllers\Participant;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\BonusPrediction;
use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $user */
        $user = auth()->user();

        $windowStart = now()->startOfDay();
        $windowEnd = now()->addDays(4)->endOfDay();

        $baseWindowQuery = MatchGame::query()
            ->whereIn('status', ['scheduled', 'live'])
            ->whereBetween('match_date', [$windowStart, $windowEnd]);

        $upcomingMatches = (clone $baseWindowQuery)
            ->with(['homeTeam', 'awayTeam'])
            ->orderByRaw('CASE WHEN DATE(match_date) = ? THEN 0 ELSE 1 END', [now()->toDateString()])
            ->orderBy('match_date')
            ->limit(20)
            ->get();

        $pendingPredictions = (clone $baseWindowQuery)
            ->openForPrediction()
            ->whereDoesntHave('predictions', fn ($query) => $query->where('user_id', $user->id))
            ->count();

        $points = (int) Prediction::query()->where('user_id', $user->id)->sum('points')
            + (int) (BonusPrediction::query()->where('user_id', $user->id)->value('points') ?? 0);

        $rankingIds = $this->rankingQuery()->pluck('users.id');
        $position = $rankingIds->search($user->id);
        $rankingPosition = $position === false ? null : $position + 1;

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

        $prizes = [
            1 => [
                'name' => AppSetting::getValue('prize_first_place'),
                'image_path' => AppSetting::getValue('prize_first_place_image_path'),
            ],
            2 => [
                'name' => AppSetting::getValue('prize_second_place'),
                'image_path' => AppSetting::getValue('prize_second_place_image_path'),
            ],
            3 => [
                'name' => AppSetting::getValue('prize_third_place'),
                'image_path' => AppSetting::getValue('prize_third_place_image_path'),
            ],
        ];

        $configuredRevealAt = AppSetting::getValue('prize_reveal_at');
        $finalMatch = $allMatches
            ->filter(fn (MatchGame $matchGame) => $this->detectBracketRound($matchGame->phase) === 'final')
            ->sortBy('match_date')
            ->first();
        $prizesRevealAt = $configuredRevealAt
            ? Carbon::parse($configuredRevealAt)->startOfDay()
            : $finalMatch?->match_date?->copy()->startOfDay();
        $prizesAreRevealed = $prizesRevealAt !== null && now()->startOfDay()->greaterThanOrEqualTo($prizesRevealAt);

        return view('participant.dashboard', [
            'upcomingMatches' => $upcomingMatches,
            'pendingPredictions' => $pendingPredictions,
            'userPoints' => $points,
            'rankingPosition' => $rankingPosition,
            'bracketRounds' => $bracketRounds,
            'finishedBracketMatches' => $finishedBracketMatches,
            'prizes' => $prizes,
            'prizesRevealAt' => $prizesRevealAt,
            'prizesAreRevealed' => $prizesAreRevealed,
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
                'key' => 'round_of_32',
                'label' => 'Dieciseisavos',
                'slots' => 16,
                'keywords' => ['dieciseisavos', 'dieciseisavo', 'treintaidosavos', 'ronda de 32', 'round of 32', 'round_of_32', 'r32', '1/16'],
            ],
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

    private function rankingQuery()
    {
        $predictionStats = Prediction::query()
            ->selectRaw('user_id')
            ->selectRaw('COALESCE(SUM(points), 0) as prediction_points')
            ->selectRaw('COALESCE(SUM(CASE WHEN is_exact_score = 1 THEN 1 ELSE 0 END), 0) as exact_scores')
            ->selectRaw('COALESCE(SUM(CASE WHEN is_correct_result = 1 THEN 1 ELSE 0 END), 0) as correct_results')
            ->groupBy('user_id');

        $bonusStats = BonusPrediction::query()
            ->selectRaw('user_id')
            ->selectRaw('COALESCE(SUM(points), 0) as bonus_points')
            ->groupBy('user_id');

        return User::query()
            ->leftJoinSub($predictionStats, 'prediction_stats', fn ($join) => $join->on('users.id', '=', 'prediction_stats.user_id'))
            ->leftJoinSub($bonusStats, 'bonus_stats', fn ($join) => $join->on('users.id', '=', 'bonus_stats.user_id'))
            ->where('users.role', 'participant')
            ->where('users.status', 'active')
            ->orderByDesc(DB::raw('COALESCE(prediction_stats.prediction_points, 0) + COALESCE(bonus_stats.bonus_points, 0)'))
            ->orderByDesc(DB::raw('COALESCE(prediction_stats.exact_scores, 0)'))
            ->orderByDesc(DB::raw('COALESCE(prediction_stats.correct_results, 0)'))
            ->orderBy('users.name');
    }
}
