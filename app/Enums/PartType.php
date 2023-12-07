<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
 * @method static static Eyes()
 * @method static static Ears()
 * @method static static Mouth()
 * @method static static Horn()
 * @method static static Back()
 * @method static static Tail()
 */
final class PartType extends Enum implements LocalizedEnum
{
    const Eyes = 'eyes';
    const Ears = 'ears';
    const Mouth = 'mouth';
    const Horn = 'horn';
    const Back = 'back';
    const Tail = 'tail';

    public function toArray()
    {
        return $this;
    }
}
