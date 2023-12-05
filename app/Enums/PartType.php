<?php

namespace App\Enums;

use BenSampo\Enum\Contracts\LocalizedEnum;
use BenSampo\Enum\Enum;

/**
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
