<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PurchaseStatus;
use App\Http\Controllers\Controller;
use App\Models\AutoPurchase;
use App\Models\QueryMonitor;
use App\Models\QueryMonitorRecord;
use Illuminate\Http\Request;

class QueryMonitorController extends Controller
{
    public function index()
    {
        $query = QueryMonitor::query()
            ->when(request()->keyword, function ($q) {
                return $q->where(function ($q2) {
                    $likeVal = '%' . request()->keyword . '%';
                    return $q2->where('query_name', 'like', $likeVal)->orWhere('mp_query_url', 'like', $likeVal);
                });
            })
            ->with(['auto_purchase']);
        return paginate_result($query);
    }

    public function store()
    {
        $params = request()->validate([
            'query_name' => 'required',
            'mp_query_url' => 'required',
        ]);
        $monitor = get_entity(QueryMonitor::class);
        $monitor->fill($params);
        $monitor->save();
    }

    public function listRecords()
    {
        $queryId = request()->get('query_id', 0);
        $query = QueryMonitorRecord::query()
            ->where('query_id', $queryId)
            ->orderByDesc('id');

        return paginate_result($query);
    }

    /** 查询监控记录汇总 */
    public function getMonitorSummary()
    {
        $queryId = request()->get('query_id', 0);
        $oneDayAgo = now()->subDay();
        $twoDayAgo = now()->subDays(2);
        $query = QueryMonitorRecord::query()->where('query_id', $queryId)
            ->where('floor_price', '>', 0);
        $todayRange = $query->clone()
            ->where('created_at', '>=', $oneDayAgo)
            ->select([\DB::raw('min(floor_price) as min_floor'), \DB::raw('max(floor_price) as max_floor')])
            ->first();
        $yesterdayRange = $query->clone()
            ->where('created_at', '>=', $twoDayAgo)
            ->where('created_at', '<', $oneDayAgo)
            ->select([\DB::raw('min(floor_price) as min_floor'), \DB::raw('max(floor_price) as max_floor')])
            ->first();

        $result = [
            'today_min_floor' => number_format($todayRange->min_floor, 0),
            'today_max_floor' => number_format($todayRange->max_floor),
            'yesterday_min_floor' => number_format($yesterdayRange->min_floor),
            'yesterday_max_floor' => number_format($yesterdayRange->max_floor),
        ];

        /** @var AutoPurchase $autoPurchase */
        $autoPurchase = AutoPurchase::query()->where('query_monitor_id', $queryId)->first();
        if ($autoPurchase) {
            $result = array_merge($result, [
                'auto_purchase' => true,
                'max_purchase_count' => $autoPurchase->max_purchase_count,
                'purchase_submit_count' => $autoPurchase->records()->count(),
                'purchase_success_count' => $autoPurchase->records()->where('status', PurchaseStatus::DONE)->count(),
            ]);
        }

        return $result;

    }

    public function destroy($id)
    {
        //删除QueryMonitorRecord
        QueryMonitorRecord::query()->where('query_id', $id)->delete();
        //删除QueryMonitor
        QueryMonitor::destroy($id);
    }
}
