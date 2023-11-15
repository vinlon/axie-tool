<?php

namespace App\Console\Commands;

use App\Models\FighterAxie;
use App\Models\Leaderboard;
use Illuminate\Console\Command;
use Ndarproj\AxieGeneParser\AxieGene;
use Ndarproj\AxieGeneParser\HexType;

class ParseGeneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'axie:parse_gene';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从gene中分析提取axie的class和parts';

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
    public function handle()
    {
        $lastTeamIds = Leaderboard::query()->pluck('last_team_id')->toArray();
        $query = FighterAxie::query()
            ->whereNull('class')
            ->whereIn('team_id', $lastTeamIds);
        $count = $query->count();
        $this->output->writeln('待处理的记录数量:' . $count);
        //每次只处理100条记录
        $axies = $query->orderByDesc('id')->limit(100)->get();
        /** @var FighterAxie $axie */
        foreach ($axies as $axie) {
            if ($axie->axie_type === 'starter') {
                //起始axie通过查询历史数据获取基因数据
                if (FighterAxie::parseGeneFromHistory($axie)) {
                    $this->output->write('.');
                } else {
                    $this->output->write('x');
                }
                continue;
            }
            try {
                $geneStr = $axie->gene;
                $gene = new AxieGene($geneStr, HexType::Bit512);
                $axie->class = $gene->getCls();
                foreach (['eyes', 'ears', 'horn', 'mouth', 'back', 'tail'] as $partType) {
                    $axie->setAttribute($partType . '_part_id', \Arr::get($gene->getGenes(), $partType . '.d.partId'));
                    $axie->setAttribute($partType . '_part_name', \Arr::get($gene->getGenes(), $partType . '.d.name'));
                }
                $axie->save();
                $this->output->write('.');
            } catch (\Exception $e) {
                $axie->class = '';
                $axie->save();
                $this->output->write('x');
                continue;
            }
        }
        return 0;
    }
}
