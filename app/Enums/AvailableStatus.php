<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static ENABLED()
 * @method static static DISABLED()
 */
final class AvailableStatus extends Enum implements LocalizedEnum
{
    public const ENABLED = 'enabled';
    public const DISABLED = 'disabled';

    public function toArray()
    {
        return $this;
    }
}
