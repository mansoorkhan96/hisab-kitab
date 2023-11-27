<?php

namespace App\Enums;

use App\Enums\Concerns\Values;

enum QuantityUnits: string
{
    use Values;

    case KiloGram = 'kg';
    case Sack = 'sack';
    case Hour = 'hour';
    case Bottle = 'bottle';
}
