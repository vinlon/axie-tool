<?php

namespace App\Services;

class MavisService
{
    private $apiKey;
    private $apiHost = 'https://api-gateway.skymavis.com/';

    public function __construct()
    {
        $this->apiKey = config('services.mavis.api_key');
    }

    /** 查询当前排行榜 */
    public function listLeaderBoard($page = 1, $limit = 100)
    {
        $offset = ($page - 1) * $limit;
        return $this->get('origins/v2/leaderboards', [
            'offset' => $offset,
            'limit' => $limit,
        ])->json('_items');
    }

    /** 查询战斗日志 */
    public function listPvpBattleHistories($userId, $page = 1, $limit = 5)
    {
        return $this->get('x/origin/battle-history', [
            'page' => $page,
            'limit' => $limit,
            'type' => 'pvp',
            'client_id' => $userId,
        ])->json('battles');
    }

    /** 查询所有 runes, charms, crafting materials, free starter Axies, etc */
    public function listAllItems()
    {
        return $this->get('origins/v2/community/items', [])->json('_items');
    }


    private function get($uri, $params)
    {
        $headers = [
            'X-API-Key' => $this->apiKey,
            'accept' => 'application/json',
        ];
        $url = $this->apiHost . $uri;
        return \Http::timeout(5)->withHeaders($headers)->get($url, $params);
    }

}
