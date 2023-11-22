<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AxieSoldHistory;

class AxieController extends Controller
{
    public function listParts()
    {
        AxieSoldHistory::query();
    }

    public function listSoldHistories()
    {
        $query = AxieSoldHistory::query()
            ->when(request()->cls, function ($q) {
                return $q->where('class', request()->cls);
            })
            ->when(request()->order_by_desc, function ($q) {
                return $q->orderByDesc(request()->order_by_desc);
            })
            ->when(request()->order_by_asc, function ($q) {
                return $q->orderBy(request()->order_by_asc);
            })
            ->when(request()->start_date, function ($q) {
                return $q->whereDate('trans_time', '>=', request()->start_date);
            })
            ->when(request()->end_date, function ($q) {
                return $q->whereDate('trans_time', '<=', request()->end_date);
            })
            ->when(request()->start_price, function ($q) {
                return $q->where('price', '>=', toWei(request()->start_price));
            })
            ->when(request()->end_price, function ($q) {
                return $q->where('price', '<=', toWei(request()->end_price));
            })
            ->orderByDesc('id');

        return paginate_result($query, function (AxieSoldHistory $history) {
            $history->price = toEth($history->price);
            $history->price_usd = number_format($history->price_usd, 2);
            return $history;
        });
    }
}
