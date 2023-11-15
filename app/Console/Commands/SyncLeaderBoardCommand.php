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
    protected $signature = 'leaderboard:sync {--page=1}';

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
        $page = $this->option('page');
        $limit = 10;
        $list = Leaderboard::query()->get()->mapWithKeys(function (Leaderboard $item) {
            return [$item->user_id => $item];
        });
        $this->output->writeln('开始同步排行榜数据: page=' . $page);
        $changeCount = 0;
        $data = $mavisService->listLeaderBoard($page, $limit);
        foreach ($data as $row) {
            $userId = \Arr::get($row, 'userID');
            $vstar = \Arr::get($row, 'vstar');
            $item = \Arr::get($list, $userId, new Leaderboard());
            if ($item->vstar != $vstar) {
                $changeCount++;
                //如果积分发生变化，则同步战斗记录
                $syncCacheKey = BattleHistory::getSyncCacheKey($userId);
                if (!\Cache::has($syncCacheKey)) {
                    $delay = rand(0, 20);
                    SyncBattleHistoryJob::dispatch($userId)->delay($delay);
                }
                //更新排行榜数据
                $item->user_id = $userId;
                $item->user_name = \Arr::get($row, 'name');
                $item->vstar = $vstar;
                $item->save();
                $this->output->write('-');
            } else {
                $this->output->write('.');
            }
        }
        $this->output->writeln('');
        $this->output->writeln("同步完成, 有{$changeCount}人积分发生变化");
        return 0;
    }
}
