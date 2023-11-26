<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AxieBodyPart;
use App\Models\AxieSoldHistory;
use App\Services\AxieService;
use Carbon\Carbon;
use Vinlon\Laravel\LayAdmin\PaginateResponse;

class AxieController extends Controller
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

    public function listParts()
    {
        return AxieBodyPart::query()->get()->map(function (AxieBodyPart $part) {
            return [
                'value' => $part->part_id,
                'name' => $part->part_type . '-' . $part->part_name,
            ];
        });
    }

    /** 查询在售记录 */
    public function listOrders()
    {
        $parts = explode(',', request()->get('parts', ''));
        $class = request()->get('cls');
        $page = intval(request()->get('page', 1));
        $limit = intval(request()->get('limit', 10));
        $url = 'https://app.axieinfinity.com/marketplace/axies?1=1';
        if ($class) {
            $url .= '&classes=' . $class;
        }
        foreach ($parts as $part) {
            $url .= '&parts=' . $part;
        }
        $result = $this->axieService->listAxiesByMarketPlaceUrl($url, $page, $limit);
        $list = [];
        foreach (\Arr::get($result, 'axies.results') as $item) {
            $row = [
                'axie_id' => \Arr::get($item, 'id'),
                'cls' => \Arr::get($item, 'class'),
                'breed_count' => \Arr::get($item, 'breedCount'),
                'current_price' => toEth(\Arr::get($item, 'order.currentPrice')),
                'started_at' => Carbon::createFromTimestamp(\Arr::get($item, 'order.startedAt'))->toDateTimeString()
            ];
            $endedAt = \Arr::get($item, 'order.endedAt', 0);
            if ($endedAt > 0) {
                $row['ended_at'] = Carbon::createFromTimestamp($endedAt)->toDateTimeString();
                $row['ended_price'] = toEth(\Arr::get($item, 'order.endedPrice'));
            }
            $list[] = $row;
        }
        return new PaginateResponse(\Arr::get($result, 'axies.total'), $list);
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
