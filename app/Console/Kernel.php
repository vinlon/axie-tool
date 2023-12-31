<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('query:purchase')->everyMinute()->runInBackground(); //自动购买axie, 买分钟执行一次
        $schedule->command('query:monitor')->everyFiveMinutes(); //监测axie价格及数量
        $schedule->command('land:monitor')->everyThirtyMinutes(); //监测Land价格
//        $schedule->command('purchase:confirm')->everyMinute(); //确认Axie购买结果

        $schedule->command('erc1155:sync_sold_history')->everyMinute(); //Rune&Charm销售记录
        $schedule->command('axie:sync_sold_history')->everyMinute(); //同步Axie销售记录
        $schedule->command('axie:parse_gene')->everyMinute(); //基因提取
        $schedule->command('axie:query_gene')->everyMinute(); //基因查询
        $schedule->command('axie:sync_eggs')->everyMinute(); //同步AxieEgg数据
        $schedule->command('axie:sync_egg_parents')->everyMinute(); //同步AxieEggParents数据
        $schedule->command('team:label')->everyMinute(); //标记队伍类型

        $schedule->command('horizon:snapshot')->everyFiveMinutes();

        $schedule->command('leaderboard:sync --page=1')->everyTwoMinutes(); //排行榜
        $schedule->command('leaderboard:sync --page=2')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=3')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=4')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=5')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=6')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=7')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=8')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=9')->everyTwoMinutes();
        $schedule->command('leaderboard:sync --page=10')->everyTwoMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
