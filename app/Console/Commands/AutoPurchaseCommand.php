<?php

namespace App\Console\Commands;

use App\Constant;
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
        $start = time();
        while (true) {
            $current = time();
            if ($current - $start >= 50) {
                break;
            }
            sleep(1);
            $monitors = QueryMonitor::query()
                ->with(['auto_purchase'])
                ->whereHas('auto_purchase')
                ->where('status', AvailableStatus::ENABLED)
                ->get();
            $this->writeln('开始执行自动购买脚本, 任务数量:' . count($monitors));
            /** @var QueryMonitor $monitor */
            foreach ($monitors as $monitor) {
                $autoPurchase = $monitor->auto_purchase;
                $purchaseCount = $autoPurchase->records()
                    ->whereIn('status', [PurchaseStatus::DONE, PurchaseStatus::WAITING])->count();
                $leftCount = $autoPurchase->max_purchase_count - $purchaseCount;
                if ($leftCount <= 0) {
                    $this->writeln($monitor->query_name . ':已完成购买任务');
                    continue;
                }
                $result = $axieService->listAxiesByMarketPlaceUrl($monitor->mp_query_url, 0, min($leftCount, 20));

                //自动购买
                $axies = Arr::get($result, 'axies.results', []);
                if (count($axies) == 0) {
                    continue;
                }
                $floorPrice = Arr::get($axies, '0.order.currentPrice');
                $this->writeln($monitor->query_name . ':floor_price=' . $floorPrice);
                $tryTimes = 0;
                foreach ($axies as $axie) {
                    $price = Arr::get($axie, 'order.currentPrice');
                    if (!$price) {
                        continue;
                    }
                    if ($price <= $autoPurchase->max_purchase_price) {
                        $axieId = Arr::get($axie, 'id');
                        if ($purchaseCount < $autoPurchase->max_purchase_count) {
                            $cacheKey = 'axie_is_purchasing:' . $axieId;
                            if (\Cache::get($cacheKey)) {
                                continue;
                            }
                            $tryTimes++;
                            \Cache::set($cacheKey, true, 60);
                            AxiePurchaseJob::dispatch($axie, $autoPurchase);
                            $purchaseCount++;
                        }
                    }
                }
                $this->writeln($monitor->query_name . ':执行完成，自动购买数量' . $tryTimes);
            }
        }

        return Command::SUCCESS;
    }

    private function writeln($msg)
    {
        $this->output->writeln(sprintf('[%s] %s', now()->toDateTimeString(), $msg));
    }
}
