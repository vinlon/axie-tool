<?php

namespace App\Http\Controllers\Api;

use App\Enums\PartType;
use App\Http\Controllers\Controller;
use App\Models\FighterAxie;
use App\Models\Leaderboard;
use App\Services\DataMap;
use Carbon\Carbon;

class HomeController extends Controller
{
    /** 排行榜 */
    public function leaderboard()
    {
        $page = request()->get('page', 1);
        $size = request()->get('size', 10);
        $maxSize = 1000;
        $topRank = ($page - 1) * $size;
        $lastUpdateTime = Leaderboard::query()
            ->limit($maxSize)
            ->select([\DB::raw('max(updated_at) as last_update_time')])
            ->value('last_update_time');

        $list = Leaderboard::query()
            ->with(['team.axies', 'user'])
            ->skip($size * ($page - 1))->limit($size)
            ->orderByDesc('vstar')
            ->get()->map(function (Leaderboard $item) use (&$topRank) {
                $team = $item->team;
                return [
                    'top_rank' => ++$topRank,
                    'user' => [
                        'user_id' => $item->user->id,
                        'ronin_address' => $item->user->ronin_address,
                        'user_name' => $item->user->nick_name,
                        'rns_name' => $item->user->rns_name,
                        'profile_name' => $item->user->profile_name,
                    ],
                    'vstar' => $item->vstar,
                    'last_active_time' => $item->last_active_time,
                    'last_active_time_display' => display_time($item->last_active_time),
                    'idle_minutes' => Carbon::now()->diffInMinutes($item->last_active_time),
                    'team' => [
                        'label' => $team->type_label,
                        'sub_label' => $team->type_sub_label,
                        'axies' => $team->axies->map(function (FighterAxie $axie) {
                            $rune = DataMap::getRune($axie->rune);
                            $parts = [];
                            foreach (PartType::getValues() as $type) {
                                $partId = $axie->getAttribute($type . '_part_id');
                                $partInfo = DataMap::getPart($partId);
                                $charmId = $axie->getAttribute($type . '_charm');
                                $charm = DataMap::getCharm($charmId);
                                $parts[] = [
                                    'type' => $type,
                                    'class' => \Arr::get($partInfo, 'class'),
                                    'part_id' => $partId,
                                    'part_name' => $axie->getAttribute($type . '_part_name'),
                                    'charm' => [
                                        'id' => $charmId,
                                        'token_id' => $charm->token_id,
                                        'image_url' => $charm->logo_url ?: 'https://storage.googleapis.com/origin-production/assets/item/' . $charmId . '.png',
                                        'desc' => $charm->description,
                                    ],
                                ];
                            }
                            return [
                                'axie_id' => $axie->axie_id,
                                'axie_url' => sprintf('https://axiecdn.axieinfinity.com/axies/%s/axie/axie-full-transparent.png', $axie->axie_id),
                                'class' => strtolower($axie->class),
                                'rune' => [
                                    'id' => $axie->rune,
                                    'token_id' => $rune->token_id,
                                    'image_url' => $rune->logo_url ?: 'https://storage.googleapis.com/origin-production/assets/item/' . $axie->rune . '.png',
                                    'desc' => $rune->description,
                                ],
                                'parts' => $parts,
                            ];
                        })
                    ]
                ];
            });
        return [
            'last_update_time' => $lastUpdateTime,
            'list' => $list,
        ];
    }

    /** 队伍类型统计 */
    public function teamSummary($from, $to)
    {
        $list = Leaderboard::query()
            ->with(['team'])->orderByDesc('vstar')
            ->offset($from - 1)->limit($to - $from + 1)->get();
        $summary = [];
        /** @var Leaderboard $item */
        foreach ($list as $index => $item) {
            $teamType = '??';
            if ($item->team) {
                $teamType = $item->team->type_label ?: '??';
            }
            arr_incr($summary, $teamType);
        }
        arsort($summary);
        $result = [];
        $top = array_splice($summary, 0, 10);
        foreach ($top as $type => $count) {
            $result[] = [
                "name" => $type,
                "value" => $count,
            ];
        }
        if (!empty($summary)) {
            $result[] = [
                'name' => '其它',
                'value' => array_sum($summary),
            ];
        }
        return $result;
    }
}
