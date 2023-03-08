<?php

namespace App\Models;

/**
 * @mixin IdeHelperAutoPurchase
 */
class AutoPurchase extends BaseModel
{
    public function records()
    {
        return $this->hasMany(AutoPurchaseRecord::class, 'auto_purchase_id');
    }
}
