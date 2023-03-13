<?php

namespace App\Console\Commands;

use App\Models\CharmSoldHistory;
use App\Services\AxieService;
use Arr;
use Illuminate\Console\Command;

class CharmSoldHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sold_history:charm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步Charm销售数据';

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
        $result = $axieService->listRecentlySoldCharms(0, 200);
        $list = Arr::get($result, 'results');
        $batchValues = [];
        foreach ($list as $row) {
            $transHash = Arr::get($row, 'transferHistory.results.0.txHash');
            if (CharmSoldHistory::query()->where('trans_hash', $transHash)->count() > 0) {
                break;
            }
            $batchValues[] = [
                'token_id' => Arr::get($row, 'tokenId'),
                'from' => Arr::get($row, 'transferHistory.results.0.from'),
                'to' => Arr::get($row, 'transferHistory.results.0.to'),
                'trans_hash' => $transHash,
                'trans_time' => \Carbon\Carbon::createFromTimestamp(Arr::get($row, 'transferHistory.results.0.timestamp'))->toDateTimeString(),
                'price' => Arr::get($row, 'transferHistory.results.0.withPrice'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        CharmSoldHistory::query()->insert($batchValues);

        $this->output->writeln(sprintf('同步Charm销售数据 %s 条', count($batchValues)));
        return 0;
    }
}
