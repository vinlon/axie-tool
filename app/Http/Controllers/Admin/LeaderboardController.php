<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Erc1155Token;
use App\Models\FighterTeam;
use App\Models\Leaderboard;
use Arr;
use Carbon\Carbon;

class LeaderboardController extends Controller
{
    public function listTeamLabels()
    {
        $teamIds = Leaderboard::query()->orderByDesc('vstar')->limit(1000)->pluck('last_team_id');
        return FighterTeam::query()
            ->select([
                'type_label',
                \DB::raw('count(*) as n')
            ])
            ->whereIn('id', $teamIds)
            ->groupBy(['type_label'])
            ->orderByDesc('n')
            ->get()->map(function (FighterTeam $team) {
                return [
                    'type_label' => $team->type_label ?: '其它',
                    'count' => $team->n,
                ];
            });
    }

    /** 排行榜队伍类型汇总 */
    public function getSummary()
    {
        $list = Leaderboard::query()->with(['team'])->orderByDesc('vstar')->limit(1000)->get();
        $summary = [];
        /** @var Leaderboard $item */
        foreach ($list as $index => $item) {
            $idleMinutes = Carbon::now()->diffInMinutes($item->last_active_time ?: Carbon::now()->subDay());
            $activeInFiveMinutes = $idleMinutes <= 5;
            $activeInTenMinutes = $idleMinutes <= 10;
            $isTop100 = $index <= 99;
            $teamType = '未知';
            if ($item->team) {
                $teamType = $item->team->type_label ?: '未知';
            }
            if ($isTop100) {
                arr_incr($summary, "top100.all");
                arr_incr($summary, "top100.per_type.{$teamType}.all");
                $summary = Arr::add($summary, "top100.per_type.{$teamType}.type", $teamType);
                if ($activeInFiveMinutes) {
                    arr_incr($summary, "top100.active_5");
                    arr_incr($summary, "top100.per_type.{$teamType}.active_5");
                }
                if ($activeInTenMinutes) {
                    arr_incr($summary, "top100.active_10");
                    arr_incr($summary, "top100.per_type.{$teamType}.active_10");
                }
            }
            arr_incr($summary, "top1000.total");
            arr_incr($summary, "top1000.per_type.{$teamType}.all");
            $summary = Arr::add($summary, "top1000.per_type.{$teamType}.type", $teamType);
            if ($activeInFiveMinutes) {
                arr_incr($summary, "top1000.active_5");
                arr_incr($summary, "top1000.per_type.{$teamType}.active_5");
            }
            if ($activeInTenMinutes) {
                arr_incr($summary, "top1000.active_10");
                arr_incr($summary, "top1000.per_type.{$teamType}.active_10");
            }
        }
        // 对每个 per_type 下的数组按 all 值降序进行排序
        usort($summary['top100']['per_type'], function ($a, $b) {
            return $b['all'] - $a['all'];
        });
        usort($summary['top1000']['per_type'], function ($a, $b) {
            return $b['all'] - $a['all'];
        });
        return $summary;
    }

    public function index()
    {
        $rankMap = $this->getUserMap();
        $runeMap = $this->getRuneMap();

        $query = Leaderboard::query()
            ->with(['team.axies'])
            ->whereIn('user_id', array_keys($rankMap))
            ->when(request()->keyword, function ($q) {
                return $q->where(function ($q2) {
                    $likeVal = '%' . request()->keyword . '%';
                    return $q2->where('user_name', 'like', $likeVal);
                });
            })
            ->when(request()->team_label, function ($q) {
                return $q->whereHas('team', function ($q2) {
                    $teamLabel = request()->team_label;
                    if ($teamLabel == '其它') {
                        return $q2->whereNull('type_label');
                    } else {
                        return $q2->where('type_label', request()->team_label);
                    }
                });
            })
            ->orderByDesc('vstar')->orderBy('last_active_time');

        return paginate_result($query, function (Leaderboard $item) use ($rankMap, $runeMap) {
            $item->top_rank = \Arr::get($rankMap, $item->user_id . '.top_rank', '-');
            if ($item->team) {
                $this->appendRuneInfo($item->team, $runeMap);
            }
            return $item;
        });
    }


    private function appendRuneInfo(FighterTeam &$team, $runeMap)
    {
        foreach ($team->axies as &$axie) {
            $runeId = $axie->rune;
            $runeId = str_replace('_nondec', '_nft', $runeId);
            if (!\Str::endsWith($runeId, '_nft')) {
                $runeId .= '_nft';
            }
            $axie['rune_info'] = \Arr::get($runeMap, $runeId);
        }
    }

    private function getRuneMap()
    {
        return Erc1155Token::query()
            ->where('type', 'rune')
            ->get()->mapWithKeys(function (Erc1155Token $token) {
                return [$token->item_id => $token];
            })->toArray();
    }

    private function getUserMap()
    {
        $rank = 1;
        return Leaderboard::query()
            ->orderByDesc('vstar')
            ->orderBy('last_active_time')
            ->limit(1000)
            ->get()->mapWithKeys(function (Leaderboard $item) use (&$rank) {
                return [$item->user_id => ['user_name' => $item->user_name, 'top_rank' => $rank++]];
            })->toArray();
    }
}
