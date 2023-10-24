<?php

namespace App\Jobs;

use App\Models\BattleHistory;
use App\Models\FighterAxie;
use App\Models\FighterTeam;
use App\Models\Leaderboard;
use App\Services\AxieService;
use App\Services\MavisService;
use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ndarproj\AxieGeneParser\AxieGene;
use Ndarproj\AxieGeneParser\HexType;

class SyncBattleHistoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $userId;

    /** @var AxieService */
    private $axieService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MavisService $mavisService, AxieService $axieService)
    {
        $this->axieService = $axieService;
        \Cache::delete($this->getCacheKey($this->userId));
        $histories = $mavisService->listPvpBattleHistories($this->userId, 0, 10);
        //查询已存在的battle
        $existBattleIds = BattleHistory::query()->whereIn('battle_uuid', \Arr::pluck($histories, 'battle_uuid'))->pluck('battle_uuid')->toArray();
        foreach (array_reverse($histories) as $index => $history) {
            $uuid = \Arr::get($history, 'battle_uuid');
            if (in_array($uuid, $existBattleIds)) {
                continue;
            }
            $battleType = \Arr::get($history, 'battle_type_string');
            $firstFighterId = \Arr::get($history, 'client_ids.0');
            $secondFighterId = \Arr::get($history, 'client_ids.1');
            $winner = \Arr::get($history, 'winner');
            $firstRank = \Arr::get($history, 'top_ranks.0', 0);
            $secondRank = \Arr::get($history, 'top_ranks.1', 0);
            $battle = new BattleHistory();
            $battle->battle_uuid = $uuid;
            $battle->first_fighter_id = $firstFighterId;
            $battle->second_fighter_id = $secondFighterId;
            $battle->first_fighter_team_id = $this->parseTeam($firstFighterId, \Arr::get($history, 'first_client_fighters'));
            $battle->second_fighter_team_id = $this->parseTeam($secondFighterId, \Arr::get($history, 'second_client_fighters'));;
            $battle->winner_id = $winner == 0 ? $firstFighterId : ($winner == 1 ? $secondFighterId : '');
            $battle->loser_id = $winner == 0 ? $secondFighterId : ($winner == 1 ? $firstFighterId : '');
            $battle->first_rank = $firstRank;
            $battle->second_rank = $secondRank;
            $battle->battle_type = $battleType;
            $battle->battle_start_time = Carbon::createFromTimestamp(\Arr::get($history, 'started_time'));
            $battle->battle_end_time = Carbon::createFromTimestamp(\Arr::get($history, 'ended_time'));
            $battle->is_surrender = \Arr::get($history, 'did_player_surrender');
            $battle->save();

            $defaultLeaderBoardValues = [
                'user_name' => '',
                'vstar' => 0,
            ];
            $first = Leaderboard::query()->firstOrNew([
                'user_id' => $firstFighterId,
            ], $defaultLeaderBoardValues);
            $second = Leaderboard::query()->firstOrNew([
                'user_id' => $secondFighterId,
            ], $defaultLeaderBoardValues);

            if ($battleType == 'ranked_pvp') {
                //如果是排名模式，则更新leaderboard
                $firstVstar = \Arr::get($history, 'rewards.0.new_vstar');
                $secondVstar = \Arr::get($history, 'rewards.1.new_vstar');
                //只更新前1000
                if ($firstRank > 0 && $firstRank <= 1000) {
                    if ($first->last_active_time == null || Carbon::parse($first->last_active_time)->lt($battle->battle_end_time)) {
                        if ($firstVstar) {
                            $first->vstar = $firstVstar;
                        }
                        $first->last_team_id = $battle->first_fighter_team_id;
                        $first->last_active_time = $battle->battle_end_time;
                        $first->save();
                    }
                    $this->scheduleNextSync($firstFighterId);
                }
                if ($secondRank > 0 && $secondRank <= 1000) {
                    if ($second->last_active_time == null || Carbon::parse($second->last_active_time)->lt($battle->battle_end_time)) {
                        if ($secondVstar) {
                            $second->vstar = $secondVstar;
                        }
                        $second->last_team_id = $battle->second_fighter_team_id;
                        $second->last_active_time = $battle->battle_end_time;
                        $second->save();
                    }
                    $this->scheduleNextSync($secondFighterId);
                }
            }
        }
    }

    private function scheduleNextSync($userId)
    {
        $cacheKey = $this->getCacheKey($userId);
        //查询用户当前是否有执行中的任务
        if (\Cache::has($cacheKey)) {
            return;
        }
        $delay = rand(60 * 3, 60 * 5);
        \Cache::set($cacheKey, now(), $delay);
        self::dispatch($userId)->delay($delay);
    }

    private function getCacheKey($userId)
    {
        return 'battle_history_syncing:' . $userId;
    }

    private function parseTeam($userId, array $fighters)
    {
        $axies = [];
        foreach ($fighters as $fighter) {
            $axieId = \Arr::get($fighter, 'axie_id', '');
            $arr = [
                'axie_id' => $axieId,
                'axie_type' => \Arr::get($fighter, 'axie_type', ''),
                'rune' => \Arr::get($fighter, 'runes.0', ''),
                'position' => \Arr::get($fighter, 'position', 0),
                'gene' => \Arr::get($fighter, 'gene', ''),
            ];
            foreach (\Arr::get($fighter, 'charms', []) as $partType => $charm) {
                $arr[$partType . '_charm'] = $charm ?: '';
            }
            $axies[] = $arr;

        }
        $teamHash = md5(json_encode($axies));
        $team = FighterTeam::query()->where('team_hash', $teamHash)->first();
        if (!$team) {
            //新建Team
            $team = new FighterTeam();
            $team->user_id = $userId;
            $team->team_hash = $teamHash;
            $team->save();
            //创建TeamAxie
            foreach ($axies as &$axie) {
                $axie['team_id'] = $team->id;
                $axie['user_id'] = $userId;
                $axie['created_at'] = now();
                $axie['updated_at'] = now();
            }
            FighterAxie::query()->insert($axies);
        }
        return $team->id;
    }
}
