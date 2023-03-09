<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\AutoPurchase
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $query_monitor_id
 * @property int $max_purchase_price 最高购买价格
 * @property int $max_purchase_count 最大购买数量
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\AutoPurchaseRecord[] $records
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchase query()
 */
	class IdeHelperAutoPurchase {}
}

namespace App\Models{
/**
 * App\Models\AutoPurchaseRecord
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $auto_purchase_id
 * @property int $axie_id AxieID
 * @property string $owner Axie持有人
 * @property int $price 购买价格
 * @property string $trans_hash 交易HASH
 * @property string $status 购买状态
 * @property string|null $remark 备注信息
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchaseRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchaseRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchaseRecord query()
 */
	class IdeHelperAutoPurchaseRecord {}
}

namespace App\Models{
/**
 * App\Models\LandMonitorRecord
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $land_type 土地类型
 * @property int $on_sale 在售数量
 * @property int $floor_price_eth 地板价ETH
 * @property float $floor_price_usd 地板价USD
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LandMonitorRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LandMonitorRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\LandMonitorRecord query()
 * @mixin \Eloquent
 */
	class IdeHelperLandMonitorRecord {}
}

namespace App\Models{
/**
 * App\Models\QueryMonitor
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $query_name 查询名称
 * @property string $mp_query_url MarketPlace查询地址
 * @property int $duration 监测间隔，分钟
 * @property string $status 状态
 * @property-read \App\Models\AutoPurchase|null $auto_purchase
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitor query()
 */
	class IdeHelperQueryMonitor {}
}

namespace App\Models{
/**
 * App\Models\QueryMonitorRecord
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $query_id
 * @property int $on_sale 在售数量
 * @property int $floor_price 地板价
 * @property int $floor_axie_id 最低价axieID
 * @property int $average_price 均价，只取价格最低的特定数量axie
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitorRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitorRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitorRecord query()
 */
	class IdeHelperQueryMonitorRecord {}
}

namespace App{
/**
 * App\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 */
	class IdeHelperUser {}
}

