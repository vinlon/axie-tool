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
 * @property-read \App\Models\QueryMonitor|null $query_monitor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AutoPurchaseRecord> $records
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchase newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchase newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchase query()
 * @mixin \Eloquent
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
 * @property \App\Enums\PurchaseStatus $status 购买状态
 * @property string|null $remark 备注信息
 * @property-read \App\Models\AutoPurchase|null $auto_purchase
 * @property-read mixed $display_eth_price
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchaseRecord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchaseRecord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AutoPurchaseRecord query()
 * @mixin \Eloquent
 */
	class IdeHelperAutoPurchaseRecord {}
}

namespace App\Models{
/**
 * App\Models\AxieBodyPart
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $version 版本
 * @property int $origin_card_id Origin Card ID
 * @property string $part_id 部位ID
 * @property string $part_type 部位类型
 * @property string $cls 种族
 * @property string $part_name 部位名称
 * @property string $ability_type 能力类型
 * @property string $description 能力描述
 * @property int $energy 能量消耗
 * @property int $attack 攻击
 * @property int $defense 护盾
 * @property int $healing 治疗
 * @property string $special_genes 特殊基因
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieBodyPart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieBodyPart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieBodyPart query()
 * @mixin \Eloquent
 */
	class IdeHelperAxieBodyPart {}
}

namespace App\Models{
/**
 * App\Models\AxieEggs
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $axie_id AxieId
 * @property string $birth_time 出生时间
 * @property string $owner_address 所有人地址
 * @property string|null $owner_name
 * @property int $matron_id 母亲ID
 * @property int $sire_id 父亲ID
 * @property string|null $matron_class
 * @property string|null $matron_breed_count
 * @property string|null $matron_eyes_part_id
 * @property string|null $matron_ears_part_id
 * @property string|null $matron_horn_part_id
 * @property string|null $matron_mouth_part_id
 * @property string|null $matron_back_part_id
 * @property string|null $matron_tail_part_id
 * @property string|null $sire_class
 * @property string|null $sire_breed_count
 * @property string|null $sire_eyes_part_id
 * @property string|null $sire_ears_part_id
 * @property string|null $sire_horn_part_id
 * @property string|null $sire_mouth_part_id
 * @property string|null $sire_back_part_id
 * @property string|null $sire_tail_part_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieEggs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieEggs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieEggs query()
 * @mixin \Eloquent
 */
	class IdeHelperAxieEggs {}
}

namespace App\Models{
/**
 * App\Models\AxieSoldHistory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $axie_id AXIE ID
 * @property string $class 品种
 * @property int $breed_count 繁殖次数
 * @property int $is_origin 是否是Origin
 * @property int $is_mystic 是否是神秘
 * @property int $japan_part_count japan部位数量
 * @property int $xmas_part_count xmas部位数量
 * @property int $summer_part_count summer部位数量
 * @property int $axp_level AXP等级
 * @property int $price 交易价格
 * @property string $price_usd 交易USD价格
 * @property string $trans_hash 交易哈希
 * @property string $trans_time 交易时间
 * @property string $from 卖方地址
 * @property string $to 买方地址
 * @property string|null $eyes_part_id
 * @property string|null $eyes_part_name
 * @property string|null $ears_part_id
 * @property string|null $ears_part_name
 * @property string|null $horn_part_id
 * @property string|null $horn_part_name
 * @property string|null $mouth_part_id
 * @property string|null $mouth_part_name
 * @property string|null $back_part_id
 * @property string|null $back_part_name
 * @property string|null $tail_part_id
 * @property string|null $tail_part_name
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieSoldHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieSoldHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AxieSoldHistory query()
 * @mixin \Eloquent
 */
	class IdeHelperAxieSoldHistory {}
}

namespace App\Models{
/**
 * App\Models\BattleHistory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $battle_uuid battle ID
 * @property string $first_fighter_id 玩家1用户ID
 * @property string $second_fighter_id 玩家2用户ID
 * @property int $first_fighter_team_id 玩家1阵容ID
 * @property int $second_fighter_team_id 玩家2阵容ID
 * @property int|null $first_rank 玩家1战斗前排名
 * @property int|null $first_old_vstar 玩家1战斗前积分
 * @property int|null $first_new_vstar 玩家1战斗后积分
 * @property int|null $second_rank 玩家2战斗前排名
 * @property int|null $second_old_vstar 玩家2战斗前积分
 * @property int|null $second_new_vstar 玩家2战斗后积分
 * @property string $winner_id 胜方用户ID
 * @property string $loser_id 败方用户ID
 * @property string $battle_type 战斗类型
 * @property string $battle_start_time 战斗开始
 * @property string $battle_end_time 战斗结束时间
 * @property int $is_surrender 是否是投降
 * @property-read \App\Models\FighterTeam|null $first_team
 * @property-read \App\Models\Leaderboard|null $first_user
 * @property-read \App\Models\FighterTeam|null $second_team
 * @property-read \App\Models\Leaderboard|null $second_user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BattleHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BattleHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BattleHistory query()
 * @mixin \Eloquent
 */
	class IdeHelperBattleHistory {}
}

namespace App\Models{
/**
 * App\Models\Erc1155SoldHistory
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $type Token Type
 * @property string $token_id Token ID
 * @property string $from 出售人
 * @property string $to 购买人
 * @property string $trans_hash 交易Hash
 * @property string $trans_time 交易时间
 * @property int $price 成交价格
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Erc1155SoldHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Erc1155SoldHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Erc1155SoldHistory query()
 * @mixin \Eloquent
 */
	class IdeHelperErc1155SoldHistory {}
}

namespace App\Models{
/**
 * App\Models\Erc1155Token
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $token_id TokenId
 * @property string $item_id ItemId
 * @property string $type 装备类型: rune/charm
 * @property int $season_id SeasonId
 * @property string $season_name Season名称
 * @property string $name 装备名称
 * @property string $class 适用种族
 * @property string $rarity 稀有度
 * @property string $logo_url LOGO图片链接
 * @property string $description 装备描述
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Erc1155Token newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Erc1155Token newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Erc1155Token query()
 * @mixin \Eloquent
 */
	class IdeHelperErc1155Token {}
}

namespace App\Models{
/**
 * App\Models\FighterAxie
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $user_id 用户ID
 * @property int $team_id 队伍ID
 * @property int $axie_id AXIE编号
 * @property string $axie_type AXIE类型
 * @property int $position AXIE站位
 * @property string $gene AXIE基因
 * @property string $rune RuneID
 * @property string|null $class AXIE种族
 * @property string|null $eyes_part_id
 * @property string|null $eyes_part_name
 * @property string $eyes_charm
 * @property string|null $ears_part_id
 * @property string|null $ears_part_name
 * @property string $ears_charm
 * @property string|null $horn_part_id
 * @property string|null $horn_part_name
 * @property string $horn_charm
 * @property string|null $mouth_part_id
 * @property string|null $mouth_part_name
 * @property string $mouth_charm
 * @property string|null $back_part_id
 * @property string|null $back_part_name
 * @property string $back_charm
 * @property string|null $tail_part_id
 * @property string|null $tail_part_name
 * @property string $tail_charm
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FighterAxie newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FighterAxie newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FighterAxie query()
 * @mixin \Eloquent
 */
	class IdeHelperFighterAxie {}
}

namespace App\Models{
/**
 * App\Models\FighterTeam
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $user_id 用户编号
 * @property string $team_hash 队伍哈希标识
 * @property string|null $type_label 队伍类型标记
 * @property string|null $type_sub_label 队伍类型二级标记
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FighterAxie> $axies
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FighterTeam newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FighterTeam newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FighterTeam query()
 * @mixin \Eloquent
 */
	class IdeHelperFighterTeam {}
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
 * App\Models\Leaderboard
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $user_id 用户ID
 * @property string $user_name 用户昵称
 * @property int $vstar 分数
 * @property int|null $last_team_id 最新使用的队伍ID
 * @property string|null $last_active_time 最近一次活跃时间
 * @property-read \App\Models\FighterTeam|null $team
 * @property-read \App\Models\OriginUser|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Leaderboard newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Leaderboard newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Leaderboard query()
 * @mixin \Eloquent
 */
	class IdeHelperLeaderboard {}
}

namespace App\Models{
/**
 * App\Models\OriginUser
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $user_id Origin游戏ID
 * @property string $ronin_address RONIN钱包地址
 * @property string $rns_name RNS域名
 * @property string|null $nick_name 游戏昵称
 * @property string $profile_name 账号昵称
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OriginUser newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OriginUser newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OriginUser query()
 * @mixin \Eloquent
 */
	class IdeHelperOriginUser {}
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
 * @property \App\Enums\AvailableStatus $status 状态
 * @property-read \App\Models\AutoPurchase|null $auto_purchase
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\QueryMonitor query()
 * @mixin \Eloquent
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
 * @mixin \Eloquent
 */
	class IdeHelperQueryMonitorRecord {}
}

namespace App\Models{
/**
 * App\Models\TeamLabelRule
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TeamLabelRule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TeamLabelRule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\TeamLabelRule query()
 * @mixin \Eloquent
 */
	class IdeHelperTeamLabelRule {}
}

namespace App{
/**
 * App\User
 *
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\User query()
 * @mixin \Eloquent
 */
	class IdeHelperUser {}
}

