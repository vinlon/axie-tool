<?php

namespace App\Console\Commands;

use App\Models\Erc1155Token;
use App\Services\GameService;
use Arr;
use Illuminate\Console\Command;

class Erc1155TokenSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'erc1155_token:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '同步ERC1155 Token数据（Rune And Charm）';

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
    public function handle(GameService $gameService)
    {
        $this->output->writeln('1. 清空原有数据');
        Erc1155Token::query()->truncate();
        $this->syncCharms($gameService);
        $this->output->writeln('2. 同步Rune数据');
        $this->syncRunes($gameService);
        $this->output->writeln('3. 同步Charm数据');
        return 0;
    }

    private function syncCharms(GameService $gameService)
    {
        $charms = $gameService->listCharms();

        $batchValues = [];
        foreach ($charms as $item) {
            $seasonId = Arr::get($item, 'season.id');
            $tokenId = Arr::get($item, 'item.tokenId', 0);
            if ($seasonId < 4) {
                continue;
            }
            if (empty($tokenId)) {
                continue;
            }
            $batchValues[] = [
                'type' => 'charm',
                'class' => Arr::get($item, 'class'),
                'season_id' => $seasonId,
                'season_name' => Arr::get($item, 'season.name'),
                'token_id' => empty($tokenId) ? 0 : $tokenId,
                'name' => Arr::get($item, 'item.name'),
                'rarity' => Arr::get($item, 'item.rarity'),
                'logo_url' => Arr::get($item, 'item.imageUrl'),
                'description' => Arr::get($item, 'item.description'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Erc1155Token::query()->insert($batchValues);
    }

    private function syncRunes(GameService $gameService)
    {
        $runes = $gameService->listRunes();
        $batchValues = [];
        foreach ($runes as $item) {
            $seasonId = Arr::get($item, 'season.id');
            $tokenId = Arr::get($item, 'item.tokenId', 0);
            if ($seasonId < 4) {
                continue;
            }
            if (empty($tokenId)) {
                continue;
            }
            $batchValues[] = [
                'type' => 'rune',
                'class' => Arr::get($item, 'class'),
                'season_id' => $seasonId,
                'season_name' => Arr::get($item, 'season.name'),
                'token_id' => empty($tokenId) ? 0 : $tokenId,
                'name' => Arr::get($item, 'item.name'),
                'rarity' => Arr::get($item, 'item.rarity'),
                'logo_url' => Arr::get($item, 'item.imageUrl'),
                'description' => Arr::get($item, 'item.description'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        Erc1155Token::query()->insert($batchValues);
    }
}
