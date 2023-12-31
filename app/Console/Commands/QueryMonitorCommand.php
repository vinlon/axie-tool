<?php

namespace App\Console\Commands;

use App\Enums\AvailableStatus;
use App\Enums\PurchaseStatus;
use App\Jobs\AxiePurchaseJob;
use App\Models\QueryMonitor;
use App\Models\QueryMonitorRecord;
use App\Services\AxieService;
use Arr;
use Illuminate\Console\Command;

class QueryMonitorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:monitor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Axie查询监测';

    /**
     * Create a new command instance.
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
        $monitors = QueryMonitor::query()
            ->where('status', AvailableStatus::ENABLED)
            ->get();
        /** @var QueryMonitor $monitor */
        foreach ($monitors as $monitor) {
            $this->output->writeln('query_monitor:' . $monitor->id);
            $result = $axieService->listAxiesByMarketPlaceUrl($monitor->mp_query_url, 0, 10);
            $total = Arr::get($result, 'axies.total');
            $floorPrice = 0;
            $floorAxieId = 0;
            $averagePrice = 0;
            if ($total > 0) {
                $axies = Arr::get($result, 'axies.results', []);
                foreach ($axies as $axie) {
                    if (!Arr::get($axie, 'order')) {
                        continue;
                    }
                    $floorPrice = Arr::get($axie, 'order.currentPrice');
                    $floorAxieId = Arr::get($axie, 'id');
                    break;
                }
                $priceArr = array_filter(Arr::pluck(Arr::get($result, 'axies.results'), 'order.currentPrice'));
                $averagePrice = intval(array_sum($priceArr) / count($priceArr));
            }
            $record = new QueryMonitorRecord();
            $record->query_id = $monitor->id;
            $record->on_sale = $total;
            $record->floor_price = $floorPrice;
            $record->floor_axie_id = $floorAxieId;
            $record->average_price = $averagePrice;
            $record->save();
        }
        return Command::SUCCESS;
    }
}
