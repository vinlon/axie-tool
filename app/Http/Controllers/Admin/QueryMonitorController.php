<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QueryMonitor;
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

    public function destroy()
    {

    }
}
