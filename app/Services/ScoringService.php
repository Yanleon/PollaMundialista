<?php

namespace App\Services;

use App\Models\MatchGame;
use App\Models\Prediction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    public function calculatePredictionPoints(Prediction $prediction): array
    {
        $matchGame = $prediction->matchGame;

        if (! $matchGame || $matchGame->home_score === null || $matchGame->away_score === null) {
            return [
                'points' => 0,
                'is_exact_score' => false,
                'is_correct_result' => false,
            ];
        }

        $actualHome = (int) $matchGame->home_score;
        $actualAway = (int) $matchGame->away_score;
        $predictedHome = (int) $prediction->predicted_home_score;
        $predictedAway = (int) $prediction->predicted_away_score;

        $isExactScore = $predictedHome === $actualHome && $predictedAway === $actualAway;

        if ($isExactScore) {
            return [
                'points' => 5,
                'is_exact_score' => true,
                'is_correct_result' => true,
            ];
        }

        $actualResult = $this->resultType($actualHome, $actualAway);
        $predictedResult = $this->resultType($predictedHome, $predictedAway);
        $isCorrectResult = $actualResult === $predictedResult;

        $points = 0;

        if ($isCorrectResult) {
            $points += 3;
        }

        if ($predictedHome === $actualHome) {
            $points += 1;
        }

        if ($predictedAway === $actualAway) {
            $points += 1;
        }

        return [
            'points' => $points,
            'is_exact_score' => false,
            'is_correct_result' => $isCorrectResult,
        ];
    }

    public function recalculateMatchPredictions(MatchGame $matchGame): void
    {
        if ($matchGame->home_score === null || $matchGame->away_score === null) {
            return;
        }

        DB::transaction(function () use ($matchGame): void {
            $predictions = $matchGame->predictions()->with('matchGame', 'user')->get();

            $userIds = [];

            foreach ($predictions as $prediction) {
                $result = $this->calculatePredictionPoints($prediction);

                $prediction->update([
                    'points' => $result['points'],
                    'is_exact_score' => $result['is_exact_score'],
                    'is_correct_result' => $result['is_correct_result'],
                ]);

                $userIds[] = $prediction->user_id;
            }

            User::query()
                ->whereIn('id', array_unique($userIds))
                ->get()
                ->each(function (User $user): void {
                    $this->recalculateUserTotal($user);
                });
        });
    }

    public function recalculateUserTotal(User $user): int
    {
        $predictionPoints = (int) $user->predictions()->sum('points');
        $bonusPoints = (int) ($user->bonusPrediction()->value('points') ?? 0);

        return $predictionPoints + $bonusPoints;
    }

    private function resultType(int $homeScore, int $awayScore): string
    {
        if ($homeScore > $awayScore) {
            return 'home';
        }

        if ($awayScore > $homeScore) {
            return 'away';
        }

        return 'draw';
    }
}
