<?php

namespace App\Console\Commands;

use App\Models\LandMonitorRecord;
use App\Services\AxieService;
use Illuminate\Console\Command;

class LandMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'land:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '土地价格监控';

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
    public function handle(AxieService $axieService)
    {
        $landTypes = ['Savannah', 'Forest', 'Arctic', 'Mystic'];
        foreach ($landTypes as $type) {
            $result = $axieService->getOnSaleLands(0, 1, $type);
            $record = new LandMonitorRecord();
            $record->land_type = $type;
            $record->on_sale = \Arr::get($result, 'total');
            $record->floor_price_eth = \Arr::get($result, 'results.0.order.currentPrice', 0);
            $record->floor_price_usd = \Arr::get($result, 'results.0.order.currentPriceUsd', 0);
            $record->save();
        }
        return Command::SUCCESS;
    }
}
