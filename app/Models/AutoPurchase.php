<?php

namespace App\Models;

/**
 * @mixin IdeHelperAutoPurchase
 */
class AutoPurchase extends BaseModel
{
    public function query_monitor()
    {
        return $this->belongsTo(QueryMonitor::class);
    }

    public function records()
    {
        return $this->hasMany(AutoPurchaseRecord::class, 'auto_purchase_id');
    }
}
