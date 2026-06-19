<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\BonusPrediction;
use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
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

        $rankingQuery = User::query()
            ->leftJoinSub($predictionStats, 'prediction_stats', fn ($join) => $join->on('users.id', '=', 'prediction_stats.user_id'))
            ->leftJoinSub($bonusStats, 'bonus_stats', fn ($join) => $join->on('users.id', '=', 'bonus_stats.user_id'))
            ->where('users.role', 'participant')
            ->where('users.status', 'active')
            ->select('users.id', 'users.name', 'users.department')
            ->selectRaw('COALESCE(prediction_stats.prediction_points, 0) + COALESCE(bonus_stats.bonus_points, 0) as total_points')
            ->selectRaw('COALESCE(prediction_stats.exact_scores, 0) as exact_scores')
            ->selectRaw('COALESCE(prediction_stats.correct_results, 0) as correct_results')
            ->orderByDesc('total_points')
            ->orderByDesc('exact_scores')
            ->orderByDesc('correct_results')
            ->orderBy('users.name');

        $topRanking = (clone $rankingQuery)->limit(3)->get();
        $ranking = $rankingQuery->paginate(25);

        $finalMatch = MatchGame::query()
            ->where('phase', 'like', '%final%')
            ->where('phase', 'not like', '%semi%')
            ->orderBy('match_date')
            ->first();

        $configuredRevealAt = AppSetting::getValue('prize_reveal_at');
        $fallbackRevealAt = $configuredRevealAt
            ? Carbon::parse($configuredRevealAt)->startOfDay()
            : $finalMatch?->match_date?->copy()->startOfDay();

        $prizes = collect([
            1 => ['name_key' => 'prize_first_place', 'image_key' => 'prize_first_place_image_path', 'reveal_key' => 'prize_first_place_reveal_at'],
            2 => ['name_key' => 'prize_second_place', 'image_key' => 'prize_second_place_image_path', 'reveal_key' => 'prize_second_place_reveal_at'],
            3 => ['name_key' => 'prize_third_place', 'image_key' => 'prize_third_place_image_path', 'reveal_key' => 'prize_third_place_reveal_at'],
        ])->map(function (array $config) use ($fallbackRevealAt): array {
            $configuredPrizeRevealAt = AppSetting::getValue($config['reveal_key']);
            $revealAt = $configuredPrizeRevealAt ? Carbon::parse($configuredPrizeRevealAt)->startOfDay() : $fallbackRevealAt;

            return [
                'name' => AppSetting::getValue($config['name_key']),
                'image_path' => AppSetting::getValue($config['image_key']),
                'reveal_at' => $revealAt,
                'is_revealed' => $revealAt !== null && now()->startOfDay()->greaterThanOrEqualTo($revealAt),
            ];
        })->all();

        $prizesRevealAt = collect($prizes)->pluck('reveal_at')->filter()->min();
        $prizesAreRevealed = collect($prizes)->every(fn (array $prize): bool => $prize['is_revealed']);
        $canViewSecretPrizes = auth()->user()?->isAdmin() || collect($prizes)->contains(fn (array $prize): bool => $prize['is_revealed']);

        return view('leaderboard.index', compact('ranking', 'topRanking', 'prizes', 'prizesRevealAt', 'prizesAreRevealed', 'canViewSecretPrizes'));
    }
}
