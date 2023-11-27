<?php

namespace App\Enums;

use App\Enums\Concerns\Values;

enum FarmingResourceType: string
{
    use Values;

    case Seed = 'Seed';
    case Fertilizer = 'Fertilizer';
    case Implement = 'Implement';
    case Pesticide = 'Pesticide';
}
