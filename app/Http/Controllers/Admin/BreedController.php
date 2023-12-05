<?php

namespace App\Http\Controllers\Admin;

use App\Console\Commands\ParseGeneCommand;
use App\Enums\PartType;
use App\Http\Controllers\Controller;
use App\Models\AxieEggs;
use Carbon\Carbon;
use Vinlon\Laravel\LayAdmin\PaginateResponse;

class BreedController extends Controller
{
    /** 查询繁殖数据列表 */
    public function listEggs()
    {
        $query = $this->getAxieEggsQuery()->orderByDesc('axie_id');
        return paginate_result($query);
    }

    /** 查询繁殖用户统计数据 */
    public function breedUserSummary()
    {
        $condition = $this->getSqlCondition();
        $perUserSql = "
            SELECT owner_address, owner_name, count(*) as total
            FROM ax_axie_eggs {$condition}
            GROUP BY owner_address, owner_name
            ORDER BY total DESC
        ";
        $summary = \DB::select($perUserSql);
        $total = array_sum(array_column($summary, 'total'));
        $result = [];
        foreach ($summary as $row) {
            $percentage = safe_divide($row->total, $total, 2);
            if ($percentage >= 0.01) {
                $row->percentage = safe_divide($row->total, $total, 2) * 100 . '%';
                $result[] = $row;
            }
        }
        return new PaginateResponse(count($result), $result);
    }

    /** 查询繁殖部位统计数据 */
    public function breedPartSummary()
    {
        $condition = $this->getSqlCondition();
        $perPartsSql = "
            SELECT part, SUM(total) as total FROM(
                SELECT sire_eyes_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY sire_eyes_part_id
                UNION ALL
                SELECT sire_ears_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY sire_ears_part_id
                UNION ALL
                SELECT sire_mouth_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY sire_mouth_part_id
                UNION ALL
                SELECT sire_horn_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY sire_horn_part_id
                UNION ALL
                SELECT sire_back_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY sire_back_part_id
                UNION ALL
                SELECT sire_tail_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY sire_tail_part_id
                UNION ALL
                SELECT matron_eyes_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY matron_eyes_part_id
                UNION ALL
                SELECT matron_ears_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY matron_ears_part_id
                UNION ALL
                SELECT matron_mouth_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY matron_mouth_part_id
                UNION ALL
                SELECT matron_horn_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY matron_horn_part_id
                UNION ALL
                SELECT matron_back_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY matron_back_part_id
                UNION ALL
                SELECT matron_tail_part_id as part, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY matron_tail_part_id
            ) as temp
            GROUP BY part
            ORDER BY total DESC;
        ";
        $summary = \DB::select($perPartsSql);
        $total = array_sum(array_column($summary, 'total'));
        $result = [];
        foreach ($summary as $row) {
            $percentage = safe_divide($row->total, $total, 2);
            if ($percentage >= 0.01) {
                $row->percentage = safe_divide($row->total, $total, 2) * 100 . '%';
                $result[] = $row;
            }
        }
        return new PaginateResponse(count($result), $result);
    }

    /** 查询繁殖种族统计数据 */
    public function breedClassSummary()
    {
        $condition = $this->getSqlCondition();
        $perClassSql = "
            SELECT class, SUM(total) as total FROM(
                SELECT sire_class as class, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY sire_class
                UNION ALL
                SELECT matron_class as class, count(*) as total FROM ax_axie_eggs {$condition} GROUP BY matron_class
            ) as temp
            GROUP BY class
            ORDER BY total DESC;
        ";
        $summary = \DB::select($perClassSql);
        $total = array_sum(array_column($summary, 'total'));
        $result = [];
        foreach ($summary as $row) {
            $percentage = safe_divide($row->total, $total, 2);
            if ($percentage >= 0.01) {
                $row->percentage = safe_divide($row->total, $total, 2) * 100 . '%';
                $result[] = $row;
            }
        }
        return new PaginateResponse(count($result), $result);
    }

    private function getSqlCondition()
    {
        $dayRange = request()->get('day_range', 7);
        $endTime = Carbon::now()->clone();
        $startTime = Carbon::now()->clone()->subDays($dayRange);
        $sql = "where birth_time between '{$startTime->toDateTimeString()}' and '{$endTime->toDateTimeString()}'";
        if (request()->min_breed_count) {
            $sql .= ' and matron_breed_count + sire_breed_count >= ' . intval(request()->min_breed_count);
        }
        if (request()->max_breed_count) {
            $sql .= ' and matron_breed_count + sire_breed_count <= ' . intval(request()->max_breed_count);
        }
        if (request()->owner_address) {
            $sql .= ' and owner_address = ' . request()->owner_address;
        }
        return $sql;
    }

    private function getAxieEggsQuery()
    {
        $dayRange = request()->get('day_range', 7);
        $endTime = Carbon::now()->clone();
        $startTime = Carbon::now()->clone()->subDays($dayRange);
        return AxieEggs::query()
            ->where('birth_time', '>=', $startTime)
            ->where('birth_time', '<=', $endTime)
            ->when(request()->min_breed_count, function ($q) {
                return $q->whereRaw('matron_breed_count + sire_breed_count >=' . intval(request()->min_breed_count));
            })
            ->when(request()->max_breed_count, function ($q) {
                return $q->whereRaw('matron_breed_count + sire_breed_count <=' . intval(request()->max_breed_count));
            })
            ->when(request()->owner_address, function ($q) {
                return $q->where('owner_address', request()->owner_address);
            })
            ->when(request()->keyword, function ($q) {
                return $q->where(function ($q1) {
                    $likeVal = '%' . request()->keyword . '%';
                    return $q1->where('owner_name', 'like', $likeVal)->orWhere('owner_address', 'like', $likeVal);
                });
            });

    }
}
