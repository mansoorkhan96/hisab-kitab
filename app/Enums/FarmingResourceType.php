<?php

namespace App\Enums;

use App\Enums\Concerns\Values;

enum FarmingResourceType: string
{
    use Values;

    case Seed = 'seed';
    case Fertilizer = 'fertilizer';
    case Implement = 'implement';
    case Pesticide = 'pesticide';
}
