<?php

namespace App\Services;

class GameService
{
    private $host = 'https://game-api-origin.skymavis.com/v2/';

    public function listRunes()
    {
        $url = $this->host . 'runes';
        return \Arr::get($this->get($url), '_items');
    }

    public function listCharms()
    {
        $url = $this->host . 'charms';
        return \Arr::get($this->get($url), '_items');
    }

    private function get($url)
    {
        $resp = \Http::withHeaders([
            'authority' => 'game-api-origin.skymavis.com',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.0.0 Safari/537.36',
        ])->get($url);
        return $resp->json();
    }
}
