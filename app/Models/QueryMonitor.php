<?php

namespace App\Models;

use App\Enums\AvailableStatus;
use BenSampo\Enum\Traits\CastsEnums;

/**
 * @mixin IdeHelperQueryMonitor
 */
class QueryMonitor extends BaseModel
{
    use CastsEnums;

    protected $casts = [
        'status' => AvailableStatus::class,
    ];

    public function auto_purchase()
    {
        return $this->hasOne(AutoPurchase::class, 'query_monitor_id');
    }
}
