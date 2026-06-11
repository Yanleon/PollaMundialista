<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_game_id',
        'predicted_home_score',
        'predicted_away_score',
        'points',
        'is_exact_score',
        'is_correct_result',
    ];

    protected function casts(): array
    {
        return [
            'predicted_home_score' => 'integer',
            'predicted_away_score' => 'integer',
            'points' => 'integer',
            'is_exact_score' => 'boolean',
            'is_correct_result' => 'boolean',
        ];
    }

    /**
     * Scope a query to predictions with awarded points.
     */
    public function scopeWithPoints(Builder $query): Builder
    {
        return $query->where('points', '>', 0);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function matchGame(): BelongsTo
    {
        return $this->belongsTo(MatchGame::class);
    }

    /**
     * Return predicted result formatted as "home-away".
     */
    public function getPredictedResult(): string
    {
        return $this->predicted_home_score.'-'.$this->predicted_away_score;
    }
}
