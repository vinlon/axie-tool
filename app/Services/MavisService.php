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

    /** 查询战斗日志(V1) */
    public function listPvpBattleHistoriesV1($userId, $page = 1, $limit = 5)
    {
        $resp = $this->get("origin/v1/community/users/{$userId}/battle-logs", [
            'page' => $page,
            'limit' => $limit,
            'type' => 'pvp',
        ]);
        return $resp->json('battles');
    }

    /** 查询战斗日志(V2) */
    public function listPvpBattleHistoriesV2($userId, $page = 1, $limit = 5)
    {
        $resp = $this->get("origin/v2/community/users/{$userId}/battle-logs", [
            'page' => $page,
            'limit' => $limit,
            'type' => 'pvp',
        ]);
        return $resp->json('_items');
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
