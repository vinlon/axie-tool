<?php

namespace App\Console\Commands;

use App\Models\AxieEggs;
use App\Services\AxieService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AxieEggSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'axie:sync_eggs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步Axie Eggs数据';

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
        $eggs = $axieService->listAxieEggs(0, 100);
        $latestEggId = AxieEggs::query()->orderByDesc('axie_id')->value('axie_id');
        $batchValues = [];
        foreach (\Arr::get($eggs, 'axies.results') as $item) {
            $axieId = \Arr::get($item, 'id');
            if (!$latestEggId) {
                $latestEggId = $axieId - 1;
            }
            if ($axieId <= $latestEggId) {
                break;
            }
            $batchValues[] = [
                'axie_id' => $axieId,
                'owner_address' => \Arr::get($item, 'owner'),
                'owner_name' => \Arr::get($item, 'ownerProfile.name'),
                'birth_time' => Carbon::createFromTimestamp(\Arr::get($item, 'birthDate')),
                'matron_id' => \Arr::get($item, 'matronId'),
                'sire_id' => \Arr::get($item, 'sireId'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        AxieEggs::query()->insert($batchValues);
        $this->output->writeln('新增数据条数:' . count($batchValues));
        return 0;
    }
}
