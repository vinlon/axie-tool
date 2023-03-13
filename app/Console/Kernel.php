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
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('query:monitor')->everyFiveMinutes(); //监测axie价格及数量
        $schedule->command('land:monitor')->everyThirtyMinutes(); //监测Land价格
        $schedule->command('query:purchase')->everyMinute(); //自动购买Axie
        $schedule->command('purchase:confirm')->everyMinute(); //确认Axie购买结果

        $schedule->command('sold_history:rune')->everyMinute(); //Rune销售记录
        $schedule->command('sold_history:charm')->everyMinute(); //Charm销售记录
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
