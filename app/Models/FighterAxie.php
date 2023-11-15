<?php

namespace App\Models;

/**
 * @mixin IdeHelperFighterAxie
 */
class FighterAxie extends BaseModel
{
    public static function parseGeneFromHistory(FighterAxie $axie)
    {
        $exist = FighterAxie::query()
            ->where('axie_id', $axie->axie_id)
            ->whereNotNull('class')->where('class', '!=', '')
            ->first();
        if ($exist) {
            $axie->class = $exist->class;
            foreach (['eyes', 'ears', 'horn', 'mouth', 'back', 'tail'] as $partType) {
                $axie->setAttribute($partType . '_part_id', $exist->getAttribute($partType . '_part_id'));
                $axie->setAttribute($partType . '_part_name', $exist->getAttribute($partType . '_part_name'));
            }
            $axie->save();
            return true;
        } else {
            $axie->class = '';
            $axie->save();
            return false;
        }
    }
}
