<?php

namespace App\Models;

/**
 * @mixin IdeHelperTeamLabelRule
 */
class TeamLabelRule extends BaseModel
{
    public static function matchLabel(FighterTeam $team, array $rules)
    {
        $axies = $team->axies;
        $parts = [];
        $runes = [];
        foreach ($axies as $axie) {
            $runes = \Arr::add($runes, $axie->rune, 0);
            $runes[$axie->rune]++;
            foreach (['eyes', 'ears', 'horn', 'mouth', 'back', 'tail'] as $partType) {
                $partId = $axie->getAttribute($partType . '_part_id');
                if (!$partId) {
                    continue;
                }
                $parts = \Arr::add($parts, $partId, 0);
                $parts[$partId]++;
            }
        }
        $label = null;
        foreach ($rules as $rule) {
            $match = true;
            foreach (\Arr::get($rule, 'parts', []) as $partId => $count) {
                if (\Arr::get($parts, $partId) < $count) {
                    $match = false;
                    break;
                }
            }
            foreach (\Arr::get($rule, 'runes', []) as $runeId => $count) {
                $nondecRuneId = str_replace('_nft', '_nondec', $runeId);
                if (\Arr::get($runes, $runeId) < $count && \Arr::get($runes, $nondecRuneId) < $count) {
                    $match = false;
                    break;
                }
            }
            if ($match) {
                $label = \Arr::get($rule, 'label');
                break;
            }
        }
        return $label;
    }
}
