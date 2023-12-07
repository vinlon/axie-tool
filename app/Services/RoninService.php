<?php

namespace App\Services;

use App\Constant;
use Http;
use function Sodium\add;

class RoninService
{
    private $gateway = 'https://api.roninchain.com/rpc';

    public function getBlock()
    {
        return $this->request('eth_getBlockByNumber', ['latest', true]);
    }

    public function getChainId()
    {
        return $this->request('eth_chainId', []);
    }

    public function getTransactionCount($walletAddress)
    {
        return $this->request('eth_getTransactionCount', [$walletAddress, 'latest']);
    }

    /** 获取Gas费 */
    public function estimateGas($data, $from, $to)
    {
        $params = ['data' => $data, 'from' => $from, 'to' => $to];
        return $this->request('eth_estimateGas', [$params]);
    }

    /** 发送Transaction请求 */
    public function sendSignedTransaction($signedTransaction)
    {
        return $this->request('eth_sendRawTransaction', ['0x' . $signedTransaction]);
    }

    /** 查询Transaction结果 */
    public function getTransactionByHash($hash)
    {
        return $this->request('eth_getTransactionByHash', [$hash]);
    }

    /** 根据地址获取RNS域名（使用第三方接口）**/
    public function getRnsNameFromAddress($address)
    {
        $url = 'https://rns.rest/lookup/' . format_address($address);
        return Http::get($url)->json("name", "");
    }


    private function request($method, $params)
    {
        \Log::channel(Constant::LOG_CHANNEL_RONIN)->info($method . '_req', $params);
        $body = [
            'id' => time(),
            'jsonrpc' => '2.0',
            'method' => $method,
            'params' => $params,
        ];
        $headers = [
            'authority' => 'api.roninchain.com',
            'accept' => 'application/json',
            'accept-language' => 'zh,en-US;q=0.9,en;q=0.8,ja;q=0.7,zh-CN;q=0.6,zh-TW;q=0.5',
            'cache-control' => 'no-cache',
            'content-type' => 'application/json',
            'origin' => 'https://app.axieinfinity.com',
            'pragma' => 'no-cache',
            'referer' => 'https://app.axieinfinity.com/',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
        ];
        $resp = \Http::timeout(5)->withHeaders($headers)->post($this->gateway, $body);
        $result = $resp->json();
        \Log::channel(Constant::LOG_CHANNEL_RONIN)->info($method . '_resp', $result);
        if (\Arr::has($result, 'error')) {
            throw new \Exception(\Arr::get($result, 'error.message'));
        }

        return \Arr::get($result, 'result');
    }
}
