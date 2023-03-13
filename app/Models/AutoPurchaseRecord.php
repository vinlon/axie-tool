<?php

namespace App\Models;

use App\Enums\PurchaseStatus;
use BenSampo\Enum\Traits\CastsEnums;

/**
 * @mixin IdeHelperAutoPurchaseRecord
 */
class AutoPurchaseRecord extends BaseModel
{
    use CastsEnums;

    protected $casts = [
        'status' => PurchaseStatus::class
    ];

    protected $appends = [
        'display_eth_price',
    ];

    public function auto_purchase()
    {
        return $this->belongsTo(AutoPurchase::class);
    }

    public function getDisplayEthPriceAttribute()
    {
        return toEth($this->price);
    }
}
