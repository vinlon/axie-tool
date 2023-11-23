<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AxieBodyPart;
use App\Models\AxieSoldHistory;

class AxieController extends Controller
{
    public function listParts()
    {
        return AxieBodyPart::query()->get()->map(function (AxieBodyPart $part) {
            return [
                'value' => $part->part_id,
                'name' => $part->part_type . '-' . $part->part_name,
            ];
        });
    }

    public function listSoldHistories()
    {
        $query = AxieSoldHistory::query()
            ->when(request()->keyword, function ($q) {
                return $q->where(function ($q1) {
                    $addressLike = '%' . request()->keyword;
                    return $q1->where('axie_id', request()->keyword)
                        ->orWhere('from', 'like', $addressLike)
                        ->orWhere('to', 'like', $addressLike);
                });
            })
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
            ->when(request()->parts, function ($q) {
                $parts = explode(',', request()->parts);
                $partsPerType = [];
                foreach ($parts as $part) {
                    $type = explode('-', $part)[0];
                    $partsPerType = \Arr::add($partsPerType, $type, []);
                    $partsPerType[$type][] = $part;
                }
                return $q->where(function ($q1) use ($partsPerType) {
                    foreach ($partsPerType as $type => $parts) {
                        $q1->whereIn($type . '_part_id', $parts);
                    }
                    return $q1;
                });
            })
            ->orderByDesc('id');

        return paginate_result($query, function (AxieSoldHistory $history) {
            $history->price = toEth($history->price);
            $history->price_usd = number_format($history->price_usd, 2);
            return $history;
        });
    }
}
