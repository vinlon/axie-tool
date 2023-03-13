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

class AutoPurchaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'query:purchase';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Axie自动购买';

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
            ->with(['auto_purchase'])
            ->where('status', AvailableStatus::ENABLED)
            ->get();
        /** @var QueryMonitor $monitor */
        foreach ($monitors as $monitor) {
            $autoPurchase = $monitor->auto_purchase;
            if (!$autoPurchase) {
                continue;
            }
            $purchaseCount = $autoPurchase->records()
                ->whereIn('status', [PurchaseStatus::DONE, PurchaseStatus::WAITING])->count();
            $leftCount = $autoPurchase->max_purchase_count - $purchaseCount;
            if ($leftCount <= 0) {
                continue;
            }
            $result = $axieService->listAxiesByMarketPlaceUrl($monitor->mp_query_url, 0, min($leftCount, 20));
            //自动购买
            $axies = Arr::get($result, 'axies.results', []);
            foreach ($axies as $axie) {
                $price = Arr::get($axie, 'order.currentPrice');
                if ($price <= $autoPurchase->max_purchase_price) {
                    if ($purchaseCount < $autoPurchase->max_purchase_count) {
                        AxiePurchaseJob::dispatch(Arr::get($axie, 'id'), $autoPurchase);
                        $purchaseCount++;
                    }
                }
            }
        }
        return Command::SUCCESS;
    }
}
