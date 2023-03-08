<?php

namespace App\Services;

use Arr;
use Http;

class AxieService
{
    /** 获取Axie列表 */
    public function getAxieBriefList($url)
    {
        $operationName = 'GetAxieBriefList';
        $query = 'query GetAxieBriefList($auctionType: AuctionType, $criteria: AxieSearchCriteria, $from: Int, $sort: SortBy, $size: Int, $owner: String) {  axies(    auctionType: $auctionType    criteria: $criteria    from: $from    sort: $sort    size: $size    owner: $owner  ) {    total    results {      ...AxieBrief      __typename    }    __typename  }}fragment AxieBrief on Axie {  id  name  stage  class  breedCount  image  title  genes  newGenes  battleInfo {    banned    __typename  }  order {    id    currentPrice    currentPriceUsd    __typename  }  parts {    id    name    class    type    specialGenes    __typename  }  __typename}';
        $variables = [
            'from' => 0,
            'sort' => 'PriceAsc',
            'auctionType' => 'Sale',
            'criteria' => $this->parseCriteria($url),
        ];

        return $this->graphql($operationName, $query, $variables);
    }

    /** 获取单个Axie详情 */
    public function getAxie($axieId)
    {
        $operationName = 'GetAxieDetail';
        $query = 'query GetAxieDetail($axieId: ID!) { axie(axieId: $axieId) { ...AxieDetail __typename }}fragment AxieDetail on Axie { id image class chain name genes newGenes owner birthDate bodyShape class sireId sireClass matronId matronClass stage title breedCount level figure { atlas model image __typename } parts { ...AxiePart __typename } stats { ...AxieStats __typename } order { ...OrderInfo __typename } ownerProfile { name __typename } battleInfo { ...AxieBattleInfo __typename } children { id name class image title stage __typename } potentialPoints { beast aquatic plant bug bird reptile mech dawn dusk __typename } equipmentInstances { ...EquipmentInstance __typename } __typename}fragment AxieBattleInfo on AxieBattleInfo { banned banUntil level __typename}fragment AxiePart on AxiePart { id name class type specialGenes stage abilities { ...AxieCardAbility __typename } __typename}fragment AxieCardAbility on AxieCardAbility { id name attack defense energy description backgroundUrl effectIconUrl __typename}fragment AxieStats on AxieStats { hp speed skill morale __typename}fragment OrderInfo on Order { id maker kind assets { ...AssetInfo __typename } expiredAt paymentToken startedAt basePrice endedAt endedPrice expectedState nonce marketFeePercentage signature hash duration timeLeft currentPrice suggestedPrice currentPriceUsd __typename}fragment AssetInfo on Asset { erc address id quantity orderId __typename}fragment EquipmentInstance on EquipmentInstance { id: tokenId tokenId owner equipmentId alias equipmentType slot name rarity collections equippedBy __typename}';
        $variables = [
            'axieId' => $axieId,
        ];

        $result = $this->graphql($operationName, $query, $variables);
        return Arr::get($result, 'axie');
    }

    /** 添加购买记录 */
    public function addBuyAxieActivity($axie, $hash)
    {
        $operationName = 'AddActivity';
        $query = 'mutation AddActivity($action: Action!, $data: ActivityDataInput!) {createActivity(action: $action, data: $data) { result __typename}}';
        $variables = [
            'action' => 'BuyAxie',
            'data' => [
                'axieId' => Arr::get($axie, 'id'),
                'owner' => Arr::get($axie, 'owner'),
                'price' => Arr::get($axie, 'order.basePrice'),
                'txHash' => $hash,
            ],
        ];
        $result = $this->graphql($operationName, $query, $variables);
        return Arr::get($result, 'data');
    }

    public function getAxiePurchaseOrderData($axieId)
    {
        $padding = '000000000000000000000000';
        $paddingEnd = '00000000000000000000000000000000000000000000000000000000';
        $uintZero = '0000000000000000000000000000000000000000';
        $preSig = '000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000a000000000000000000000000000000000000000000000000000000000000001200000000000000000000000000000000000000000000000000000000000000041';
        $postSig = '00000000000000000000000000000000000000000000000000000000000000000000000000000000000000';
        $orderSig = 'c54c66d3';
    }

    public function parseParamsFromUrl($url)
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        if (empty($queryString)) {
            return [];
        }
        $queryPairs = explode('&', $queryString);
        $params = [];
        foreach ($queryPairs as $pair) {
            list($key, $value) = explode('=', $pair, 2);
            if (Arr::has($params, $key)) {
                if (is_array($params[$key])) {
                    $params[$key][] = $value;
                } else {
                    $params[$key] = [$params[$key], $value];
                }
            } else {
                $params[$key] = $value;
            }
        }

        return $params;
    }

    private function graphql($operationName, $query, $variables)
    {
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjFlZDY0OGYzLTZkYmQtNjkyOC05ZmNmLWM4OWY1NjIwNDg1NiIsInNpZCI6MTE5OTU0MTMyLCJyb2xlcyI6WyJ1c2VyIl0sInNjcCI6WyJhbGwiXSwiYWN0aXZhdGVkIjp0cnVlLCJhY3QiOnRydWUsInJvbmluQWRkcmVzcyI6IjB4OGZhZjJiM2YzNzhkMWNjYjc5NmIxZTNhZGIxYWRmMGExYTVlNjc5ZCIsImV4cCI6MTY3OTAwNjYxNCwiaWF0IjoxNjc3Nzk3MDE0LCJpc3MiOiJBeGllSW5maW5pdHkiLCJzdWIiOiIxZWQ2NDhmMy02ZGJkLTY5MjgtOWZjZi1jODlmNTYyMDQ4NTYifQ.N7qHNcAXWDXJP5zgZbUusYLrErbCb9QbKAmuHefEMW0';
        $gateway = 'https://graphql-gateway.axieinfinity.com/graphql';
        $headers = [
            'authority' => 'graphql-gateway.axieinfinity.com',
            'accept' => '*/*',
            'accept-language' => 'zh,en-US;q=0.9,en;q=0.8,ja;q=0.7,zh-CN;q=0.6,zh-TW;q=0.5',
            'authorization' => 'Bearer ' . $token,
            'cache-control' => 'no-cache',
            'content-type' => 'application/json',
            'cookie' => 'AF_DEFAULT_MEASUREMENT_STATUS=true; afUserId=4b80213f-f2b8-4706-a76b-d3ed02aa0055-p; __cuid=bec52dd9a7774d9fa0db6d7b60e9fe6c; amp_fef1e8=2d8ece93-09de-490e-a5be-55d6a9585266R...1gi5c9ppr.1gi5c9pq2.2.1.3; _gid=GA1.2.904165382.1674375992; _gsid=4ef23d2538b54b55bbbe7301d34b0d8b; cf_clearance=LYDRuQOlwxuEXB_OewBfa.Op2gvLZdPeo4O6_FbQ2Fg-1675780498-0-150; _ga_YGEWKD0HZL=GS1.1.1677113948.1.1.1677115600.0.0.0; AF_SYNC=1677370465791; _gat_gtag_UA_150383258_1=1; _ga=GA1.1.23810327.1668479617; _ga_VCXVN683YE=GS1.1.1677681660.471.1.1677682898.0.0.0',
            'origin' => 'https://app.axieinfinity.com',
            'pragma' => 'no-cache',
            'referer' => 'https://app.axieinfinity.com/',
            'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36',
        ];
        $data = [
            'operationName' => $operationName,
            'variables' => $variables,
            'query' => $query,
        ];
        $resp = Http::withHeaders($headers)->post($gateway, $data);
        $result = $resp->json();
        if (Arr::has($result, 'errors')) {
            throw new \Exception(Arr::get($result, 'errors.0.message'));
        }
        return Arr::get($result, 'data');
    }

    private function parseCriteria($url)
    {
        $params = $this->parseParamsFromUrl($url);
        $criteria = [];
        $criteria['parts'] = Arr::get($params, 'parts');

        return $criteria;
    }
}
