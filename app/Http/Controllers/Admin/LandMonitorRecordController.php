<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandMonitorRecord;
use Illuminate\Http\Request;

class LandMonitorRecordController extends Controller
{
    public function index()
    {
        $query = LandMonitorRecord::query();
    }
}
