<?php

namespace App\Console\Commands;

use App\Models\FighterTeam;
use App\Models\Leaderboard;
use App\Models\TeamLabelRule;
use Illuminate\Console\Command;

class TeamLabelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'team:label';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '队伍类型标记';

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
        $labelRules = [
            [
                'label' => '红眼',
                'parts' => [
                    'eyes-topaz' => 3
                ],
            ],
            [
                'label' => 'Feather鸟',
                'parts' => [
                    'horn-feather-spear' => 2,
                ],
            ],
            [
                'label' => '怒兽',
                'parts' => [
                    'ears-zen' => 2,
                ],
            ],
            [
                'label' => '怒兽',
                'parts' => [
                    'mouth-confident' => 2,
                    'tail-shiba' => 2,
                ],
            ],
            [
                'label' => '流血兽',
                'runes' => [
                    'rune_beast_4011_s6_nft' => 1,
                ],
            ],
            [
                'label' => '血毒',
                'runes' => [
                    'rune_reptile_4011_s6_nft' => 1,
                ],
                'parts' => [
                    'eyes-chubby' => 1,
                ],
            ],
            [
                'label' => '鸡冠Bubba',
                'parts' => [
                    'horn-cuckoo' => 2,
                ],
            ],
            [
                'label' => '叶子',
                'parts' => [
                    'ears-leafy' => 3,
                ],
            ],
            [
                'label' => '猫头鹰',
                'parts' => [
                    'ears-owl' => 1,
                    'mouth-little-owl' => 1,
                    'eyes-little-owl' => 1,
                ],
            ],
            [
                'label' => '塞牌',
                'parts' => [
                    'ears-swirl' => 2,
                ],
            ],
            [
                'label' => '塞牌',
                'parts' => [
                    'eyes-confused' => 2,
                ],
            ],
            [
                'label' => '拖鞋',
                'parts' => [
                    'back-sandal' => 2,
                    'tail-nimo' => 2,
                ],
            ],
            [
                'label' => '海葵',
                'parts' => [
                    'back-anemone' => 2,
                    'tail-nimo' => 2,
                ],
            ],
            [
                'label' => '生存',
                'parts' => [
                    'ears-tassels' => 1,
                ],
            ],
            [
                'label' => '生存',
                'runes' => [
                    'rune_dawn_3010_s6_nft' => 1,
                ],
            ],
            [
                'label' => '一刀流',
                'parts' => [
                    'back-perch' => 1,
                ],
            ],
            [
                'label' => '泡泡',
                'parts' => [
                    'back-sponge' => 2,
                    'ears-nimo' => 2,
                ],
            ],
            [
                'label' => 'WingHorn鸟',
                'parts' => [
                    'horn-wing-horn' => 2,
                ],
            ],
            [
                'label' => '生存',
                'parts' => [
                    'ears-tiny-fan' => 1,
                    'eyes-tricky' => 1,
                ],
            ],
            [
                'label' => '辣椒',
                'parts' => [
                    'tail-hot-butt' => 2,
                ],
            ],
            [
                'label' => 'GM兽',
                'runes' => [
                    'rune_beast_3012_s6_nft' => 1,
                ],
            ],
            [
                'label' => '毒',
                'parts' => [
                    'back-green-thorns' => 2,
                ],
            ],
            [
                'label' => 'Spike毒',
                'parts' => [
                    'back-tri-spikes' => 2,
                    'horn-scaly-spear' => 2,
                ],
            ],
            [
                'label' => 'JINX',
                'parts' => [
                    'mouth-doubletalk' => 2,
                    'tail-tadpole' => 2,
                ],
            ],
            [
                'label' => 'Bleed',
                'parts' => [
                    'eyes-chubby' => 2,
                ],
            ],
            [
                'label' => 'MOMO',
                'parts' => [
                    'ears-curved-spine' => 1,
                ],
            ],
            [
                'label' => 'Feather鸟',
                'runes' => [
                    'rune_bird_4010_s6_nft' => 1
                ],
            ],
        ];
        $list = Leaderboard::query()
            ->with(['team.axies'])
            ->whereHas('team')
            ->orderByDesc('vstar')
            ->limit(1000)->get();
        $top100UnMatchCount = 0;
        $unMatchCount = 0;
        foreach ($list as $index => $item) {
            /** @var FighterTeam $team */
            $team = $item->team;
            $label = TeamLabelRule::matchLabel($team, $labelRules);
            if ($label) {
                $team->type_label = $label;
                $team->save();
                $this->output->write('.');
            } else {
                $ranking = $index + 1;
                if ($ranking <= 100) {
                    $top100UnMatchCount++;
                }
                $unMatchCount++;
                $team->type_label = null;
                $team->save();
                $this->output->write('x');
            }
        }
        $this->output->writeln('');
        $this->output->writeln("队伍类型匹配完成，前100未匹配数量:{$top100UnMatchCount}, 前1000未匹配数量: {$unMatchCount}");
        return 0;
    }
}
