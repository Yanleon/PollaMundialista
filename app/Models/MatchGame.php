<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'phase',
        'group_name',
        'home_team_id',
        'away_team_id',
        'match_date',
        'home_score',
        'away_score',
        'status',
        'prediction_deadline',
    ];

    protected function casts(): array
    {
        return [
            'match_date' => 'datetime',
            'home_score' => 'integer',
            'away_score' => 'integer',
            'prediction_deadline' => 'datetime',
        ];
    }

    /**
     * Scope a query to scheduled matches.
     */
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope a query to finished matches.
     */
    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('status', 'finished');
    }

    /**
     * Scope a query to matches that still accept predictions.
     */
    public function scopeOpenForPrediction(Builder $query): Builder
    {
        return $query
            ->whereIn('status', ['scheduled', 'live'])
            ->where(function (Builder $inner): void {
                $inner->where('prediction_deadline', '>', now())
                    ->orWhere(function (Builder $fallback): void {
                        $fallback->whereNull('prediction_deadline')
                            ->where('match_date', '>', now());
                    });
            });
    }

    public function homeTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function predictions(): HasMany
    {
        return $this->hasMany(Prediction::class);
    }

    /**
     * Check if the match still allows prediction changes.
     */
    public function isPredictionOpen(): bool
    {
        if (! in_array($this->status, ['scheduled', 'live'], true)) {
            return false;
        }

        if ($this->prediction_deadline !== null) {
            return $this->prediction_deadline->isFuture();
        }

        return $this->match_date !== null && $this->match_date->isFuture();
    }

    /**
     * Return final result formatted as "home-away".
     */
    public function getFinalResult(): ?string
    {
        if ($this->home_score === null || $this->away_score === null) {
            return null;
        }

        return $this->home_score.'-'.$this->away_score;
    }
}
