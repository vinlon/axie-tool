<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AutoPurchaseRecord;
use Illuminate\Http\Request;

class AutoPurchaseController extends Controller
{
    public function index()
    {
        $query = AutoPurchaseRecord::query()
            ->with(['auto_purchase.query_monitor'])
            ->orderByDesc('id');

        return paginate_result($query);
    }
}
