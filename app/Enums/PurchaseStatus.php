<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static DONE()
 * @method static static FAIL()
 * @method static static CONFIRMING()
 */
final class PurchaseStatus extends Enum implements LocalizedEnum
{
    const DONE = 'done';
    const FAIL = 'fail';
    const WAITING = 'waiting';

    public function toArray()
    {
        return $this;
    }
}
