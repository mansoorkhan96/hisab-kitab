<?php

namespace App\Enums;

use App\Enums\Concerns\Values;
use Filament\Support\Contracts\HasLabel;

enum FarmingResourceType: string implements HasLabel
{
    use Values;

    case Seed = 'seed';
    case Fertilizer = 'fertilizer';
    case Implement = 'implement';
    case Pesticide = 'pesticide';

    public function getLabel(): ?string
    {
        return $this->value;
    }
}
