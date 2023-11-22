<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BattleHistory;
use App\Models\Erc1155Token;
use App\Models\FighterTeam;
use App\Models\Leaderboard;
use Vinlon\Laravel\LayAdmin\PaginateResponse;

class UserController extends Controller
{

    public function listBattleHistories()
    {
        //查询战斗记录
        $userId = request()->user_id;
        if (request()->keyword) {
            $userId = Leaderboard::query()->where('user_name', 'like', '%' . request()->keyword . '%')->value('user_id');
            if (!$userId) {
                return new PaginateResponse(0, []);
            }
        }
        $query = BattleHistory::query()
            ->with(['first_user', 'second_user', 'first_team.axies', 'second_team.axies'])
            ->when(request()->battle_type, function ($q) {
                return $q->where('battle_type', request()->battle_type);
            })
            ->when($userId, function ($q) use ($userId) {
                return $q->where(function ($q1) use ($userId) {
                    return $q1->where('first_fighter_id', $userId)->orWhere('second_fighter_id', $userId);
                });
            })
            ->orderByDesc('battle_end_time');
        $runeMap = $this->getRuneMap();
        $userMap = $this->getUserMap();
        return paginate_result($query, function (BattleHistory $history) use ($runeMap, $userMap, $userId) {
            $userId = $userId ?: $history->first_fighter_id;

            $isFirstUser = $userId == $history->first_fighter_id;
            $userTeam = $isFirstUser ? $history->first_team : $history->second_team;
            $enemyTeam = $isFirstUser ? $history->second_team : $history->first_team;
            $enemyUserId = $isFirstUser ? $history->second_fighter_id : $history->first_fighter_id;
            $this->appendRuneInfo($userTeam, $runeMap);
            $this->appendRuneInfo($enemyTeam, $runeMap);
            return [
                'uuid' => $history->battle_uuid,
                'battle_time' => $history->battle_start_time . '~' . substr($history->battle_end_time, -8),
                'is_surrender' => $history->is_surrender,
                'battle_type' => $history->battle_type,
                'is_first' => $isFirstUser,
                'user' => \Arr::get($userMap, $userId),
                'user_team' => $userTeam,
                'user_old_vstar' => $isFirstUser ? $history->first_old_vstar : $history->second_old_vstar,
                'user_new_vstar' => $isFirstUser ? $history->first_new_vstar : $history->second_new_vstar,
                'user_rank' => $isFirstUser ? $history->first_rank : $history->second_rank,
                'enemy' => \Arr::get($userMap, $enemyUserId),
                'enemy_team' => $enemyTeam,
                'enemy_old_vstar' => $isFirstUser ? $history->second_old_vstar : $history->first_old_vstar,
                'enemy_new_vstar' => $isFirstUser ? $history->second_new_vstar : $history->first_new_vstar,
                'enemy_rank' => $isFirstUser ? $history->second_rank : $history->first_rank,
                'result' => $userId == $history->winner_id ? 'win' : ($userId == $history->loser_id ? 'lose' : 'draw'),
            ];
        });
    }

    public function listTeamSummary()
    {
        $userId = request()->user_id;
        //最近100场ranked_pvp战斗
        $battleHistories = BattleHistory::query()
            ->with(['first_team.axies', 'second_team.axies'])
            ->where('battle_type', 'ranked_pvp')
            ->where(function ($q) use ($userId) {
                return $q->where('first_fighter_id', $userId)->orWhere('second_fighter_id', $userId);
            })->orderByDesc('battle_end_time')
            ->limit(100)
            ->get();

        $summary = [];
        $runeMap = $this->getRuneMap();
        $charmMap = $this->getCharmMap();
        /** @var BattleHistory $history */
        foreach ($battleHistories->reverse() as $history) {
            $isFirstUser = $userId == $history->first_fighter_id;
            $userTeam = $isFirstUser ? $history->first_team : $history->second_team;
            $enemyTeam = $isFirstUser ? $history->second_team : $history->first_team;
            $result = $userId == $history->winner_id ? 'win' : ($userId == $history->loser_id ? 'lose' : 'draw');
            $teamId = $userTeam->id;
            foreach ([
                         'all',
                         "enemy.{$enemyTeam->type_label}",
                         "teams.{$teamId}",
                         "teams.{$teamId}.enemy.{$enemyTeam->type_label}",
                     ] as $keyPrefix) {
                arr_incr($summary, "{$keyPrefix}.total");
                arr_incr($summary, "{$keyPrefix}.{$result}");
                $seq = $isFirstUser ? 'first' : 'second';
                arr_incr($summary, "{$keyPrefix}.{$seq}_total");
                arr_incr($summary, "{$keyPrefix}.{$seq}_{$result}");
            }
            $teamInfoKey = "teams.{$teamId}.info";
            if (!\Arr::has($summary, $teamInfoKey)) {
                $this->appendRuneInfo($userTeam, $runeMap);
                $this->appendCharmInfo($userTeam, $charmMap);
                \Arr::set($summary, $teamInfoKey, $userTeam);
            }
        }
        //反转team顺序
        $summary['teams'] = array_reverse($summary['teams']);
        return $summary;
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

    private function appendCharmInfo(FighterTeam &$team, $charmMap)
    {
        foreach ($team->axies as &$axie) {
            foreach (['eyes', 'ears', 'horn', 'mouth', 'back', 'tail'] as $partType) {
                $charmId = $axie->getAttribute($partType . '_charm');
                $charmId = str_replace('_nondec', '_nft', $charmId);
                if (!\Str::endsWith($charmId, '_nft')) {
                    $charmId .= '_nft';
                }
                $axie[$partType . '_charm_url'] = \Arr::get($charmMap, $charmId . '.logo_url');
            }
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

    private function getCharmMap()
    {
        return Erc1155Token::query()
            ->where('type', 'charm')
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
                return [$item->user_id => [
                    'user_id' => $item->user_id,
                    'user_name' => $item->user_name,
                    'top_rank' => $rank++
                ]];
            })->toArray();
    }
}
