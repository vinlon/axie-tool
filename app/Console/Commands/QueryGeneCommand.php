<?php

namespace App\Console\Commands;

use App\Models\FighterAxie;
use App\Models\Leaderboard;
use App\Services\AxieService;
use Illuminate\Console\Command;

class QueryGeneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'axie:query_gene';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '查询axie基因信息，用于处理starter axie或者无法正常提取基因的axie';

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
        $lastTeamIds = Leaderboard::query()->pluck('last_team_id')->toArray();
        //每次只处理10条记录
        $query = FighterAxie::query()
            ->whereIn('team_id', $lastTeamIds)
            ->where('class', '') //在axie:parse_gene命令中，处理失败会将class值设为空字符串
            ->orWhere(function ($q) {
                return $q->where('axie_type', 'starter')->whereNull('class');
            });
        $count = $query->count();
        $this->output->writeln('待提取基因的axie数量为:' . $count);
        $axies = $query->orderByDesc('id')->limit(10)->get();
        /** @var FighterAxie $axie */
        foreach ($axies as $axie) {
            if (FighterAxie::parseGeneFromHistory($axie)) {
                $this->output->write('.');
                continue;
            }
            $axieInfo = $axieService->getAxie($axie->axie_id);
            $axie->class = \Arr::get($axieInfo, 'class') ?: 'unknown';
            foreach (\Arr::get($axieInfo, 'parts', []) as $part) {
                $partType = strtolower(\Arr::get($part, 'type'));
                $axie->setAttribute($partType . '_part_id', \Arr::get($part, 'id'));
                $axie->setAttribute($partType . '_part_name', \Arr::get($part, 'name'));
            }
            $axie->save();
            $this->output->write('o');
        }
        return 0;
    }
}
