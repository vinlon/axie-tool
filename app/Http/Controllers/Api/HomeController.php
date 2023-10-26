<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FighterAxie;
use App\Models\Leaderboard;

class HomeController extends Controller
{
    public function leaderboard()
    {
        $page = request()->get('page', 1);
        $size = request()->get('size', 10);
        $topRank = ($page - 1) * $size;
        return Leaderboard::query()
            ->with(['team.axies'])
            ->skip($size * ($page - 1))->limit($size)
            ->orderByDesc('vstar')
            ->get()->map(function (Leaderboard $item) use (&$topRank) {
                $team = $item->team;
                return [
                    'top_rank' => ++$topRank,
                    'user_id' => $item->user_id,
                    'user_name' => $item->user_name,
                    'vstar' => $item->vstar,
                    'last_active_time' => $item->last_active_time,
                    'team' => [
                        'label' => $team->type_label,
                        'sub_label' => $team->type_sub_label,
                        'axies' => $team->axies->map(function (FighterAxie $axie) {
                            return [
                                'axie_id' => $axie->axie_id,
                                'class' => $axie->class,
                                'rune' => $axie->rune,
                            ];
                        })
                    ]
                ];
            });
    }
}
