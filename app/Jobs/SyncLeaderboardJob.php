<?php

namespace App\Jobs;

use App\Models\Leaderboard;
use App\Services\MavisService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Type\Integer;

class SyncLeaderboardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** @var Integer $page */
    private $page;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($page)
    {
        $this->page = $page;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(MavisService $mavisService)
    {
        //10分钟内未更新过的用户的分数归0
        Leaderboard::query()
            ->where('updated_at', '<=', Carbon::now()->subMinutes(10))
            ->update(['vstar' => 0]);
        $limit = 100;
        $list = Leaderboard::query()->get()->mapWithKeys(function (Leaderboard $item) {
            return [$item->user_id => $item];
        });
        $data = $mavisService->listLeaderBoard($this->page, $limit);
        foreach ($data as $row) {
            $userId = \Arr::get($row, 'userID');
            $vstar = \Arr::get($row, 'vstar');
            $item = \Arr::get($list, $userId, new Leaderboard());
            if ($item->vstar != $vstar) {
                //如果积分发生变化，则同步战斗记录
                $delay = rand(0, 100);
                $cacheKey = 'user_syncing_' . $userId;
                if (!\Cache::get($cacheKey, false)) {
                    SyncBattleHistoryJob::dispatch($userId)->delay($delay);
                    \Cache::set($cacheKey, true, 120);
                }
            }
            //更新排行榜数据
            $item->user_id = $userId;
            $item->user_name = \Arr::get($row, 'name');
            $item->vstar = $vstar;
            $item->updated_at = now();
            $item->save();
        }
    }
}
