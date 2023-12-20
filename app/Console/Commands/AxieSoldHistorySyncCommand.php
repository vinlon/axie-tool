<?php

namespace App\Console\Commands;

use App\Models\AxieSoldHistory;
use App\Services\AxieService;
use Arr;
use Illuminate\Console\Command;
use Str;

class AxieSoldHistorySyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'axie:sync_sold_history';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'axie销售记录同步';

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
        $axieSoldResult = $axieService->listRecentlyAxiesSold(0, 100);
        $insertCount = $this->batchInsertValues($axieSoldResult);
        $this->output->writeln(sprintf('同步Axie销售数据 %s 条', $insertCount));
        return 0;
    }

    private function batchInsertValues($rows)
    {
        /** @var AxieSoldHistory $latestHistory */
        $latestHistory = AxieSoldHistory::query()->orderByDesc('id')->first();
        $batchValues = [];
        foreach ($rows as $row) {
            $transHash = Arr::get($row, 'transferHistory.results.0.txHash');
            if ($latestHistory && $latestHistory->trans_hash == $transHash) {
                break;
            }
            $title = Arr::get($row, 'title', '');
            $insertRow = [
                'axie_id' => Arr::get($row, 'id'),
                'class' => Arr::get($row, 'class'),
                'breed_count' => Arr::get($row, 'breedCount'),
                'is_origin' => $title == 'Origin',
                'is_mystic' => Arr::get($row, 'hasMysticSuit', false),
                'is_meo' => Str::startsWith($title, 'MEO Corp'),
                'axp_level' => Arr::get($row, 'axpInfo.level'),
                'price' => Arr::get($row, 'transferHistory.results.0.withPrice'),
                'price_usd' => Arr::get($row, 'transferHistory.results.0.withPriceUsd'),
                'from' => Arr::get($row, 'transferHistory.results.0.from'),
                'to' => Arr::get($row, 'transferHistory.results.0.to'),
                'trans_hash' => $transHash,
                'trans_time' => \Carbon\Carbon::createFromTimestamp(Arr::get($row, 'transferHistory.results.0.timestamp'))->toDateTimeString(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $japanPartCount = $xmasPartCount = $summerPartCount = 0;
            foreach (Arr::get($row, 'parts', []) as $part) {
                $partType = strtolower(Arr::get($part, 'type'));
                $partId = Arr::get($part, 'id');
                $partName = Arr::get($part, 'name');
                $specialGene = Arr::get($part, 'specialGenes');
                $insertRow[$partType . '_part_id'] = $partId;
                $insertRow[$partType . '_part_name'] = $partName;
                if ($specialGene == 'Japan') {
                    $japanPartCount++;
                }
                if ($specialGene == 'Xmas2019') {
                    $xmasPartCount++;
                }
                if ($specialGene == 'Xmas2018') {
                    $xmasPartCount++;
                }
                if ($specialGene == 'Summer2022') {
                    $summerPartCount++;
                }
            }
            $insertRow['japan_part_count'] = $japanPartCount;
            $insertRow['xmas_part_count'] = $xmasPartCount;
            $insertRow['summer_part_count'] = $summerPartCount;

            $batchValues[] = $insertRow;
        }
        AxieSoldHistory::query()->insert(array_reverse($batchValues));
        return count($batchValues);
    }
}
