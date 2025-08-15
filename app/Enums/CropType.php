<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum CropType: string implements HasLabel
{
    case Wheat = 'wheat';
    case Cotton = 'cotton';

    public function getLabel(): string
    {
        return match ($this) {
            self::Wheat => 'Wheat',
            self::Cotton => 'Cotton'
        };
    }
}
