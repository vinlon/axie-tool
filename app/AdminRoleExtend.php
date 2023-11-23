<?php

namespace App;

use Illuminate\Support\Arr;
use Vinlon\Laravel\LayAdmin\AdminRole;

class AdminRoleExtend extends AdminRole
{
    const TEST = '测试';

    /**
     * @return string[]
     */
    public function getMenuIds()
    {
        $roleMenus = [
            self::TEST => ['axie_origin.tokens', 'axie_origin.axie_sales', 'axie_origin.leaderboard', 'axie_origin.battle_history'],
        ];

        return Arr::get($roleMenus, $this->value, []);
    }

    /**
     * @return string[]
     */
    public function getPrivileges()
    {
        $rolePrivileges = [
            // self::TEST => [1, 2, 3],
        ];

        return Arr::get($rolePrivileges, $this->value, []);
    }
}
