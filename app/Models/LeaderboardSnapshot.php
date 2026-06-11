<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaderboardSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_points',
        'exact_scores',
        'correct_results',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'total_points' => 'integer',
            'exact_scores' => 'integer',
            'correct_results' => 'integer',
            'calculated_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
