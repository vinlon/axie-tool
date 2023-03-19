<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Erc1155SoldHistory;
use App\Models\Erc1155Token;
use App\Services\AxieService;
use Illuminate\Support\Arr;
use Vinlon\Laravel\LayAdmin\PaginateResponse;

class SoldHistoryController extends Controller
{
    /** @var AxieService */
    private $axieService;

    /**
     * @param AxieService $axieService
     */
    public function __construct(AxieService $axieService)
    {
        $this->axieService = $axieService;
    }

    public function getSoldSummary()
    {
        $season = request()->get('season', 4);
        $type = request()->get('type', 'rune');
        $tokens = Erc1155Token::query()
            ->where('type', $type)
            ->where('season_id', 4)
            ->when(request()->rarity, function ($q) {
                return $q->where('rarity', request()->rarity);
            })
            ->get()->mapWithKeys(function (Erc1155Token $token) {
                return [$token->token_id => $token];
            });
        $summaryResult = Erc1155SoldHistory::query()
            ->where('trans_time', '>=', now()->subDay()->startOfDay())
            ->whereIn('token_id', $tokens->keys())
            ->select([
                'token_id',
                \DB::raw('DATE(trans_time) as day'),
                \DB::raw('COUNT(price) as count'),
                \DB::raw('MIN(price) as min_price'),
                \DB::raw('AVG(price) as avg_price'),
                \DB::raw('MAX(price) as max_price'),
            ])
            ->groupBy('token_id', \DB::raw('DATE(trans_time)'))
            ->get();
        $tokenDailySummary = [];
        foreach ($summaryResult as $history) {
            $tokenDailySummary[$history->token_id][$history->day] = $history->toArray();
        }

        $result = [];
        $sortCol = [];
        /** @var Erc1155Token $token */
        foreach ($tokens as $token) {
            $tokenId = $token->token_id;
            $today = now()->toDateString();
            $yesterday = now()->subDay()->toDateString();
            $dailySummary = \Arr::get($tokenDailySummary, $tokenId);
            if (!\Arr::has($dailySummary, $today) && !\Arr::has($dailySummary, $yesterday)) {
                continue;
            }
            $sortCol[] = \Arr::get($dailySummary, $today . '.' . request()->get('sort', 'count'), 0);
            $result[] = [
                'type' => $token->type,
                'rarity' => $token->rarity,
                'token_id' => $token->token_id,
                'token_name' => $token->name,
                'logo_url' => $token->logo_url,
                'today' => $this->formatSummary(\Arr::get($dailySummary, $today)),
                'yesterday' => $this->formatSummary(\Arr::get($dailySummary, $yesterday)),
            ];
        }
        array_multisort($sortCol, SORT_DESC, SORT_REGULAR, $result);
        return $result;
    }

    public function getTokenOrders()
    {
        $type = request()->get('type');
        $tokenId = request()->get('token_id');
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);
        $from = ($page - 1) * $limit;
        if ($type === 'rune') {
            $result = $this->axieService->listRuneOrders($tokenId, $from, $limit);
        } else {
            $result = $this->axieService->listCharmOrders($tokenId, $from, $limit);
        }
        $list = [];
        foreach (Arr::get($result, 'data') as $row) {
            $endPrice = Arr::get($row, 'endedPrice');
            $list[] = [
                'current_price' => toEth(Arr::get($row, 'currentPrice')),
                'current_price_usd' => Arr::get($row, 'currentPriceUsd'),
                'ended_price' => $endPrice > 0 ? toEth($endPrice) : '-',
                'started_at' => \Carbon\Carbon::createFromTimestamp(Arr::get($row, 'startedAt'))->toDateTimeString(),
            ];
        }
        return new PaginateResponse(\Arr::get($result, 'total'), $list);
    }

    public function getTokenSoldHistories()
    {
        $tokenId = request()->get('token_id');
        $query = Erc1155SoldHistory::query()
            ->where('token_id', $tokenId)
            ->orderByDesc('id');

        return paginate_result($query, function (Erc1155SoldHistory $history) {
            $history->price = toEth($history->price);
            return $history;
        });
    }

    private function formatSummary($summary)
    {
        return [
            'count' => \Arr::get($summary, 'count', 0),
            'avg_price' => toEth(\Arr::get($summary, 'avg_price', 0)),
            'min_price' => toEth(\Arr::get($summary, 'min_price', 0)),
            'max_price' => toEth(\Arr::get($summary, 'max_price', 0)),
        ];
    }
}
