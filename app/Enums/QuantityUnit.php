<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum QuantityUnit: string implements HasLabel
{
    case KiloGram = 'kg';
    case Sack = 'sack';
    case Hour = 'hour';
    case Bottle = 'bottle';
    case Acre = 'acre';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::KiloGram => 'Kg',
            self::Sack => 'Sack',
            self::Hour => 'Hour',
            self::Bottle => 'Bottle',
            self::Acre => 'Acre',
        };
    }
}
