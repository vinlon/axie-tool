<?php

namespace App\Services;

use App\Models\Erc1155Token;

class DataMap
{
    private static $runes = null;
    private static $parts = null;
    private static $charms = null;

    /**
     * @return Erc1155Token
     */
    public static function getRune($runeId)
    {
        $runeId = str_replace('_nondec', '_nft', $runeId);
        if (!\Str::endsWith($runeId, '_nft')) {
            $runeId .= '_nft';
        }
        if (!self::$runes) {
            self::$runes = Erc1155Token::query()
                ->where('type', 'rune')
                ->get()->mapWithKeys(function (Erc1155Token $token) {
                    return [$token->item_id => $token];
                });
        }
        return \Arr::get(self::$runes, $runeId, new Erc1155Token());
    }

    /**
     * @return Erc1155Token
     */
    public static function getCharm($charmId)
    {
        $charmId = str_replace('_nondec', '_nft', $charmId);
        if (!\Str::endsWith($charmId, '_nft')) {
            $charmId .= '_nft';
        }
        if (!self::$charms) {
            self::$charms = Erc1155Token::query()
                ->where('type', 'charm')
                ->get()->mapWithKeys(function (Erc1155Token $token) {
                    return [$token->item_id => $token];
                });
        }
        return \Arr::get(self::$charms, $charmId, new Erc1155Token());
    }

    public static function getPart($partId)
    {
        if (!self::$parts) {
            self::$parts = json_decode(file_get_contents(base_path('axie-gene-parser/Assets/parts.json')), true);
        };
        return \Arr::get(self::$parts, $partId, []);
    }
}
