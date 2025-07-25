<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case Admin = 'admin';
    case Farmer = 'farmer';
    case Driver = 'driver';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Farmer => 'Farmer',
            self::Driver => 'Driver',
        };
    }
}