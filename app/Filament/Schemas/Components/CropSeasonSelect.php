<?php

namespace App\Filament\Schemas\Components;

use App\Models\CropSeason;
use Filament\Forms\Components\Select;

class CropSeasonSelect
{
    public static function make(): Select
    {
        return Select::make('crop_season_id')
            ->relationship('cropSeason', 'title')
            ->searchable()
            ->preload()
            ->default(
                CropSeason::query()
                    ->where('user_id', auth()->id())
                    ->where('is_current', true)
                    ->first()
                    ->id
            )
            ->required();
    }
}
