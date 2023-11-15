<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Erc1155Token;
use App\Models\FighterTeam;
use App\Models\Leaderboard;

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

    public function index()
    {
        $rank = 1;
        $rankMap = Leaderboard::query()
            ->orderByDesc('vstar')
            ->orderBy('last_active_time')
            ->limit(1000)
            ->get()->mapWithKeys(function (Leaderboard $item) use (&$rank) {
                return [$item->id => $rank++];
            })->toArray();
        $runeMap = Erc1155Token::query()
            ->where('type', 'rune')
            ->get()->mapWithKeys(function (Erc1155Token $token) {
                return [$token->item_id => $token];
            })->toArray();
        $query = Leaderboard::query()
            ->with(['team.axies'])
            ->whereIn('id', array_keys($rankMap))
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
            $item->top_rank = \Arr::get($rankMap, $item->id, '-');
            foreach ($item->team->axies as &$axie) {
                $runeId = $axie->rune;
                $runeId = str_replace('_nondec', '_nft', $runeId);
                if (!\Str::endsWith($runeId, '_nft')) {
                    $runeId .= '_nft';
                }
                $axie['rune_info'] = \Arr::get($runeMap, $runeId);
            }
            return $item;
        });
    }
}
