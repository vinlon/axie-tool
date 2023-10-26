<?php

namespace App\Models;

/**
 * @mixin IdeHelperFighterTeam
 */
class FighterTeam extends BaseModel
{
    public function axies()
    {
        return $this->hasMany(FighterAxie::class, 'team_id');
    }
}
