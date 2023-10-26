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
        $schedule->command('query:purchase')->everyMinute()->runInBackground(); //自动购买axie, 买分钟执行一次

        $schedule->command('query:monitor')->everyFiveMinutes(); //监测axie价格及数量
        $schedule->command('land:monitor')->everyThirtyMinutes(); //监测Land价格

        $schedule->command('purchase:confirm')->everyMinute(); //确认Axie购买结果

        $schedule->command('erc1155:sync_sold_history')->everyMinute(); //Rune&Charm销售记录
        $schedule->command('leaderboard:sync')->everyFiveMinutes(); //排行榜
        $schedule->command('axie:parse_gene')->everyMinute(); //基因提取
        $schedule->command('axie:query_gene')->everyMinute(); //基因查询

        $schedule->command('horizon:snapshot')->everyFiveMinutes();
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
