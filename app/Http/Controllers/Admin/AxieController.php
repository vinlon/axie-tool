<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AxieSoldHistory;

class AxieController extends Controller
{
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
                return $q->orderByDesc(request()->order_by_asc);
            })
            ->orderByDesc('id');

        return paginate_result($query, function (AxieSoldHistory $history) {
            $history->price = toEth($history->price);
            $history->price_usd = number_format($history->price_usd, 2);
            return $history;
        });
    }
}
