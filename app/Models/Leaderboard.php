<?php

namespace App\Models;

/**
 * @mixin IdeHelperLeaderboard
 */
class Leaderboard extends BaseModel
{
    public function user()
    {
        return $this->belongsTo(OriginUser::class, 'user_id', 'user_id');
    }

    public function team()
    {
        return $this->belongsTo(FighterTeam::class, 'last_team_id');
    }
}
