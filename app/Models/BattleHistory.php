<?php

namespace App\Models;

/**
 * @mixin IdeHelperBattleHistory
 */
class BattleHistory extends BaseModel
{
    public function first_user()
    {
        return $this->belongsTo(OriginUser::class, 'first_fighter_id', 'user_id');
    }

    public function second_user()
    {
        return $this->belongsTo(OriginUser::class, 'second_fighter_id', 'user_id');
    }

    public function first_team()
    {
        return $this->belongsTo(FighterTeam::class, 'first_fighter_team_id');
    }

    public function second_team()
    {
        return $this->belongsTo(FighterTeam::class, 'second_fighter_team_id');
    }
}
