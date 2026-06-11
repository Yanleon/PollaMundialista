<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusPrediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'champion_team_id',
        'runner_up_team_id',
        'top_scorer',
        'points',
    ];

    protected function casts(): array
    {
        return [
            'champion_team_id' => 'integer',
            'runner_up_team_id' => 'integer',
            'points' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function championTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'champion_team_id');
    }

    public function runnerUpTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'runner_up_team_id');
    }
}
