<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'flag_url',
        'group_name',
        'status',
    ];

    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'home_team_id');
    }

    public function awayMatches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'away_team_id');
    }
}
