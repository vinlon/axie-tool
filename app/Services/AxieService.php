<?php

namespace App\Services;

use Arr;
use Http;

class AxieService
{
    /** 获取Axie列表 */
    public function listAxiesByMarketPlaceUrl($url, $from, $size)
    {
        $operationName = 'GetAxieBriefList';
        $query = 'query GetAxieBriefList($auctionType: AuctionType, $criteria: AxieSearchCriteria, $from: Int, $sort: SortBy, $size: Int, $owner: String) { axies( auctionType: $auctionType criteria: $criteria from: $from sort: $sort size: $size owner: $owner ) { total results { ...AxieBrief __typename } __typename }}fragment AxieBrief on Axie { id name owner stage class breedCount image title genes newGenes battleInfo { banned __typename } order { id basePrice currentPrice currentPriceUsd maker startedAt endedAt expiredAt endedPrice nonce paymentToken marketFeePercentage signature __typename }  __typename}';
        $variables = [
            'from' => $from,
            'size' => $size,
            'sort' => 'PriceAsc',
            'auctionType' => 'Sale',
        ];
        $criteria = $this->parseCriteriaFromUrl($url);
        if (!empty($criteria)) {
            $variables['criteria'] = $criteria;
        }
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

    public function listRecentlySoldCharms($from = 0, $size = 10)
    {
        return $this->getRecentlyErc1155Sold('Charm', $from, $size);
    }

    public function listRecentlySoldRune($from = 0, $size = 10)
    {
        return $this->getRecentlyErc1155Sold('Rune', $from, $size);
    }

    public function listRuneOrders($tokenId, $from = 0, $size = 10)
    {
        return $this->getErc1155TokenOrders('Rune', $tokenId, $from, $size);
    }

    public function listCharmOrders($tokenId, $from = 0, $size = 10)
    {
        return $this->getErc1155TokenOrders('Charm', $tokenId, $from, $size);
    }


    private function getRecentlyErc1155Sold($type, $from, $size)
    {
        $operationName = 'GetRecentlyErc1155Sold';
        $query = 'query GetRecentlyErc1155Sold($from: Int, $size: Int, $tokenType: Erc1155Type!) { settledAuctions { erc1155Tokens(from: $from, size: $size, tokenType: $tokenType) { total results { total id: tokenId tokenId tokenAddress tokenType transferHistory {  ...TransferHistoryInSettledAuction  __typename } __typename } __typename } __typename }}fragment TransferHistoryInSettledAuction on TransferRecords { total results { ...TransferRecordInSettledAuction __typename } __typename}fragment TransferRecordInSettledAuction on TransferRecord { from to txHash timestamp withPrice withPriceUsd fromProfile { name __typename } toProfile { name __typename } __typename}';
        $variables = [
            'from' => intval($from),
            'size' => intval($size),
            'tokenType' => $type,
        ];
        $result = $this->graphql($operationName, $query, $variables);
        return Arr::get($result, 'settledAuctions.erc1155Tokens');
    }

    private function getErc1155TokenOrders($type, $tokenId, $from, $size)
    {
        $operationName = 'GetErc1155TokenOrders';
        $query = 'query GetErc1155TokenOrders($tokenId: String!, $tokenType: Erc1155Type!, $maker: String, $from: Int!, $size: Int!, $sort: SortBy!, $owner: String) { erc1155Token(tokenType: $tokenType, tokenId: $tokenId, owner: $owner) { id: tokenId tokenId orders(maker: $maker, from: $from, size: $size, sort: $sort) { ...OrdersInfo __typename } __typename }}fragment OrdersInfo on Orders { total quantity data { ...OrderInfo __typename } __typename}fragment OrderInfo on Order { id maker kind assets { ...AssetInfo __typename } expiredAt paymentToken startedAt basePrice endedAt endedPrice expectedState nonce marketFeePercentage signature hash duration timeLeft currentPrice suggestedPrice currentPriceUsd __typename}fragment AssetInfo on Asset { erc address id quantity orderId __typename}';
        $variables = [
            'from' => intval($from),
            'size' => intval($size),
            'tokenType' => $type,
            'tokenId' => strval($tokenId),
            'sort' => 'PriceAsc',
        ];
        $result = $this->graphql($operationName, $query, $variables);
        return Arr::get($result, 'erc1155Token.orders');
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
        $this->graphql($operationName, $query, $variables);
    }

    /** 查询在售的Land */
    public function getOnSaleLands($from, $size, $landType = null)
    {
        $operationName = 'GetLandsGrid';
        $query = 'query GetLandsGrid($from: Int!, $size: Int!, $sort: SortBy!, $owner: String, $criteria: LandSearchCriteria, $auctionType: AuctionType) { lands( criteria: $criteria from: $from size: $size sort: $sort owner: $owner auctionType: $auctionType ) { total results { ...LandBriefV2 __typename } __typename }}fragment LandBriefV2 on LandPlot { tokenId owner landType row col order { id currentPrice startedAt currentPriceUsd __typename } ownerProfile { name __typename } __typename}';
        $variables = [
            'auctionType' => 'Sale',
            'from' => intval($from),
            'size' => intval($size),
            'sort' => 'PriceAsc',
        ];
        if ($landType) {
            $variables['criteria']['landType'] = [$landType];
        }
        $result = $this->graphql($operationName, $query, $variables);
        return Arr::get($result, 'lands');
    }

    public function parseCriteriaFromUrl($url)
    {
        $queryString = parse_url($url, PHP_URL_QUERY);
        $availableKeys = ['classes', 'parts', 'pureness', 'breedCount', 'title'];
        $queryPairs = explode('&', $queryString);
        $params = [];
        foreach ($queryPairs as $pair) {
            if (empty($pair) || strpos($pair, '=') === false) {
                continue;
            }
            list($key, $value) = explode('=', $pair, 2);
            if (!in_array($key, $availableKeys)) {
                continue;
            }
            $value = urldecode($value);
            if (is_numeric($value)) {
                $value = intval($value);
            }
            if (Arr::has($params, $key)) {
                $params[$key][] = $value;
            } else {
                $params[$key] = [$value];
            }
        }

        return $params;
    }

    private function graphql($operationName, $query, $variables)
    {
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpZCI6IjFlZGFjYzgxLWUyN2YtNmNhMC04MjMxLTI1NThkNDhkMDk0MyIsInNpZCI6MTI0Njk1MTU0LCJyb2xlcyI6WyJ1c2VyIl0sInNjcCI6WyJhbGwiXSwiYWN0aXZhdGVkIjp0cnVlLCJhY3QiOnRydWUsInJvbmluQWRkcmVzcyI6IjB4MWYxOTE5NzhiYWM1YmYwN2JlNzcxYWIxOGIxY2MyMGQ0Zjk0ZWE4NCIsImV4cCI6MTY3OTQ4MzI2OSwiaWF0IjoxNjc4MjczNjY5LCJpc3MiOiJBeGllSW5maW5pdHkiLCJzdWIiOiIxZWRhY2M4MS1lMjdmLTZjYTAtODIzMS0yNTU4ZDQ4ZDA5NDMifQ.yPF6z-Ushb6Pemqp0-vqkaEJetpXtbDey9dpElq-tSQ';
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
        if (Arr::has($result, 'errors') && !Arr::get($result, 'data')) {
            throw new \Exception(Arr::get($result, 'errors.0.message'));
        }
        return Arr::get($result, 'data');
    }
}
