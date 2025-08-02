<?php

namespace App\Filament\Tables\Filters;

use App\Models\CropSeason;
use Filament\Tables\Filters\SelectFilter;

class CropSeasonFilter
{
    public static function make(?string $name = 'cropSeason'): SelectFilter
    {
        return SelectFilter::make($name)
            ->label('Crop Season')
            ->relationship($name, 'title')
            ->default(CropSeason::current()->getKey())
            ->selectablePlaceholder(false);
    }
}
