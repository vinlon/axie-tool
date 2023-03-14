<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CharmSoldHistory;
use App\Models\RuneSoldHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Vinlon\Laravel\LayAdmin\PaginateResponse;

class SoldHistoryController extends Controller
{
    public function getRuneSummary()
    {
        $list = $this->getSoldSummary(RuneSoldHistory::query(), 'rune');

        $count = RuneSoldHistory::query()
            ->distinct()->count(['token_id']);

        return new PaginateResponse($count, $list);
    }

    public function getCharmSummary()
    {
        $list = $this->getSoldSummary(CharmSoldHistory::query(), 'charm');

        $count = CharmSoldHistory::query()
            ->distinct()->count(['token_id']);

        return new PaginateResponse($count, $list);
    }

    private function getSoldSummary(Builder $query, $type)
    {
        $page = request()->get('page', 1);
        $limit = request()->get('limit', 10);
        return $query->select(['token_id', \DB::raw('count(*) as count'), \DB::raw('AVG(price) as avg_price')])
            ->when(request()->order_by_desc, function ($q) {
                return $q->orderByDesc(request()->order_by_desc);
            })
            ->when(request()->order_by_asc, function ($q) {
                return $q->orderBy(request()->order_by_asc);
            })
            ->orderByDesc('count')
            ->groupBy(['token_id'])
            ->offset(($page - 1) * $limit)->limit($limit)
            ->get()->map(function ($history) use ($type) {
                return [
                    'type' => $type,
                    'token_id' => $history->token_id,
                    'count' => $history->count,
                    'avg_price' => toEth($history->avg_price)
                ];
            });
    }
}
