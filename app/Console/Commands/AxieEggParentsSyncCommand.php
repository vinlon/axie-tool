<?php

namespace App\Console\Commands;

use App\Models\AxieEggs;
use App\Services\AxieService;
use Illuminate\Console\Command;

class AxieEggParentsSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'axie:sync_egg_parents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步Axie Egg Parents数据';

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
        $eggs = AxieEggs::query()
            ->where(function ($q) {
                return $q->whereNull('matron_class')->orWhereNull('sire_class');
            })
            ->orderBy('axie_id')
            ->limit(10)
            ->get();

        $axieIds = [];
        /** @var AxieEggs $egg */
        foreach ($eggs as $egg) {
            if (!in_array($egg->matron_id, $axieIds)) {
                $axieIds[] = $egg->matron_id;
            }
            if (!in_array($egg->sire_id, $axieIds)) {
                $axieIds[] = $egg->sire_id;
            }
        }
        $axies = $axieService->listAxies($axieIds);
        foreach ($eggs as $egg) {
            foreach (['matron', 'sire'] as $prefix) {
                $id = $egg->getAttribute($prefix . '_id');
                $axie = \Arr::get($axies, $id);
                $egg->setAttribute($prefix . '_class', \Arr::get($axie, 'class'));
                $egg->setAttribute($prefix . '_breed_count', \Arr::get($axie, 'breedCount'));
                foreach (\Arr::get($axie, 'parts') as $part) {
                    $partType = strtolower(\Arr::get($part, 'type'));
                    $egg->setAttribute($prefix . '_' . $partType . '_part_id', \Arr::get($part, 'id'));
                }
            }
            $egg->save();
        }
        return 0;
    }
}
