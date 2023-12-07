<?php

namespace App\Services;

use App\Exceptions\MavisException;
use Illuminate\Http\Client\Response;

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
        $resp = $this->get('origins/v2/leaderboards', [
            'offset' => $offset,
            'limit' => $limit,
        ]);
        return $this->parseResult($resp, '_items');
    }

    /** 查询战斗日志(V1) */
    public function listPvpBattleHistoriesV1($userId, $page = 1, $limit = 5)
    {
        $resp = $this->get("origin/v1/community/users/{$userId}/battle-logs", [
            'page' => $page,
            'limit' => $limit,
            'type' => 'pvp',
        ]);
        return $this->parseResult($resp, 'battles');
    }

    /** 查询战斗日志(V2) */
    public function listPvpBattleHistoriesV2($userId, $page = 1, $limit = 5)
    {
        $resp = $this->get("origin/v2/community/users/{$userId}/battle-logs", [
            'page' => $page,
            'limit' => $limit,
            'type' => 'pvp',
        ]);
        return $this->parseResult($resp, '_items');
    }

    /** 查询所有 runes, charms, crafting materials, free starter Axies, etc */
    public function listAllItems()
    {
        $resp = $this->get('origins/v2/community/items', []);
        return $this->parseResult($resp, '_items');
    }

    private function parseResult(Response $resp, $key)
    {
        $data = $resp->json();
        if (\Arr::has($data, '_errorMessage')) {
            throw new MavisException(\Arr::get($data, '_errorMessage'));
        }
        return \Arr::get($data, $key);
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
