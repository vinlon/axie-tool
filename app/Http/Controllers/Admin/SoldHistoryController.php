<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CharmSoldHistory;
use App\Models\Erc1155Token;
use App\Models\RuneSoldHistory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Vinlon\Laravel\LayAdmin\PaginateResponse;

class SoldHistoryController extends Controller
{
    public function getRuneSummary()
    {
        $list = $this->getSoldSummary(RuneSoldHistory::query(), 'rune', $season);

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

    private function getSoldSummary($type)
    {
        $season = request()->get('season', 4);
        $tokens = Erc1155Token::query()->where('type', $type)->where('season_id', 4)->get();
        $dailySummary = RuneSoldHistory::query()
            ->select([
                \DB::raw('DATE(created_at) as day')
            ]);
    }
}
