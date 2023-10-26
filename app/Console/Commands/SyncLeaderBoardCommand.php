<?php

namespace App\Console\Commands;

use App\Jobs\SyncBattleHistoryJob;
use App\Models\BattleHistory;
use App\Models\DailyLeaderboard;
use App\Models\Leaderboard;
use App\Services\MavisService;
use Illuminate\Console\Command;

class SyncLeaderBoardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'leaderboard:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步排行榜';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(MavisService $mavisService)
    {
        //同步排行榜前1000名, 每次100,同步10次
        $limit = 100;
        $list = Leaderboard::query()->get()->mapWithKeys(function (Leaderboard $item) {
            return [$item->user_id => $item];
        });
        $this->output->writeln('开始同步排行榜数据');
        for ($page = 1; $page <= 10; $page++) {
            $data = $mavisService->listLeaderBoard($page, $limit);
            foreach ($data as $row) {
                $userId = \Arr::get($row, 'userID');
                $item = \Arr::get($list, $userId, new Leaderboard());
                $item->user_id = $userId;
                $item->user_name = \Arr::get($row, 'name');
                $item->vstar = \Arr::get($row, 'vstar');
                $item->save();
                $this->output->write('.');
            }
        }
        $this->output->writeln('');
        $this->output->writeln('排行榜同步完成');

        // 随机选择10个人，添加战斗记录同步任务
        $this->output->writeln('添加同步任务');
        $needSyncUserIds = Leaderboard::query()->inRandomOrder()->limit(10)->pluck('user_id');
        foreach ($needSyncUserIds as $userId) {
            $syncCacheKey = BattleHistory::getSyncCacheKey($userId);
            if (!\Cache::has($syncCacheKey)) {
                SyncBattleHistoryJob::dispatch($userId);
                \Cache::set($syncCacheKey, now(), 5 * 60);
            }
        }
        return 0;
    }
}
