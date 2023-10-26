<?php

namespace App\Models;

/**
 * @mixin IdeHelperLeaderboard
 */
class Leaderboard extends BaseModel
{
    public function team()
    {
        return $this->belongsTo(FighterTeam::class, 'last_team_id');
    }
}
