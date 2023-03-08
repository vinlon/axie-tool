<?php

namespace App\Models;

/**
 * @mixin IdeHelperQueryMonitor
 */
class QueryMonitor extends BaseModel
{
    public function auto_purchase()
    {
        return $this->hasOne(AutoPurchase::class, 'query_monitor_id');
    }
}
