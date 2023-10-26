<?php

namespace App\Models;

/**
 * @mixin IdeHelperBattleHistory
 */
class BattleHistory extends BaseModel
{
    public static function getSyncCacheKey($userId)
    {
        return 'battle_history_syncing:' . $userId;
    }
}
