<?php

namespace App\Console\Commands;

use App\Jobs\SyncBattleHistoryJob;
use App\Jobs\SyncLeaderboardJob;
use App\Models\Leaderboard;
use App\Services\MavisService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Laravel\Horizon\Lock;

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
        $delay = rand(0, 40);

        SyncLeaderboardJob::dispatch($page)->delay($delay);
    }
}
