<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static TEST()
 */
final class ImageResource extends Enum implements LocalizedEnum
{
    public const TEST = 'test';

    public function toArray()
    {
        return $this;
    }
}
