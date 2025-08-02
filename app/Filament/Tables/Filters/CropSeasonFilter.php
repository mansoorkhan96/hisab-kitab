<?php

namespace App\Filament\Tables\Filters;

use App\Models\CropSeason;
use Filament\Tables\Filters\SelectFilter;

class CropSeasonFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('cropSeason')
            ->label('Crop Season')
            ->relationship('cropSeason', 'title')
            ->default(CropSeason::current()->getKey())
            ->selectablePlaceholder(false);
    }
}
