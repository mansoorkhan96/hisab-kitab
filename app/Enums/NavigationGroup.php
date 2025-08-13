<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum NavigationGroup implements HasLabel
{
    case CottonCrop;

    public function getLabel(): string
    {
        return match ($this) {
            self::CottonCrop => 'Cotton Crop',
        };
    }
}
