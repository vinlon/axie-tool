<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Erc1155SoldHistory;
use App\Models\Erc1155Token;
use Vinlon\Laravel\LayAdmin\PaginateResponse;

class SoldHistoryController extends Controller
{
    public function getSoldSummary()
    {
        $season = request()->get('season', 4);
        $type = request()->get('type', 'rune');
        $tokens = Erc1155Token::query()
            ->where('type', $type)
            ->where('season_id', 4)
            ->get()->mapWithKeys(function (Erc1155Token $token) {
                return [$token->token_id => $token];
            });
        $summaryResult = Erc1155SoldHistory::query()
            ->where('trans_time', '>=', now()->subDay()->startOfDay())
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
            $sortCol[] = \Arr::get($dailySummary, $today . '.count', 0);
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

    public function formatSummary($summary)
    {
        return [
            'count' => \Arr::get($summary, 'count', 0),
            'avg_price' => toEth(\Arr::get($summary, 'avg_price', 0)),
            'min_price' => toEth(\Arr::get($summary, 'min_price', 0)),
            'max_price' => toEth(\Arr::get($summary, 'max_price', 0)),
        ];
    }
}
