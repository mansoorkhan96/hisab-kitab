<?php

namespace App\Enums;

use App\Enums\Concerns\Values;

enum QuantityUnit: string
{
    use Values;

    case KiloGram = 'kg';
    case Sack = 'sack';
    case Hour = 'hour';
    case Bottle = 'bottle';
}
