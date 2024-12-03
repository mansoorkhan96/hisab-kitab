<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum FarmingResourceType: string implements HasLabel
{
    case Seed = 'seed';
    case Fertilizer = 'fertilizer';
    case Implement = 'implement';
    case Pesticide = 'pesticide';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Seed => 'Seed',
            self::Fertilizer => 'Fertilizer',
            self::Implement => 'Implement',
            self::Pesticide => 'Pesticide',
        };
    }
}
