<?php

namespace App\Console\Commands;

use App\Models\Erc1155SoldHistory;
use App\Services\AxieService;
use Arr;
use Illuminate\Console\Command;

class ERC1155SoldHistoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erc1155:sync_sold_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步Charm和Rune销售数据';

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
        $charmResult = $axieService->listRecentlySoldCharms(0, 100);
        $charmHistory = Arr::get($charmResult, 'results');
        $insertCount = $this->batchInsertValues($charmHistory, 'charm');
        $this->output->writeln(sprintf('同步Charm销售数据 %s 条', $insertCount));

        $runeResult = $axieService->listRecentlySoldRune(0, 100);
        $runeHistory = Arr::get($runeResult, 'results');
        $insertCount = $this->batchInsertValues($runeHistory, 'rune');
        $this->output->writeln(sprintf('同步Rune销售数据 %s 条', $insertCount));
        return 0;
    }

    private function batchInsertValues($rows, $type)
    {
        /** @var Erc1155SoldHistory $latestHistory */
        $latestHistory = Erc1155SoldHistory::query()->where('type', $type)->orderByDesc('id')->first();
        $batchValues = [];
        foreach ($rows as $row) {
            $transHash = Arr::get($row, 'transferHistory.results.0.txHash');
            if ($latestHistory && $latestHistory->trans_hash == $transHash) {
                break;
            }
            $batchValues[] = [
                'type' => $type,
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
        Erc1155SoldHistory::query()->insert(array_reverse($batchValues));
        return count($batchValues);
    }
}
