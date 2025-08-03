<?php

namespace App\Filament\Resources\CropSeasons\Pages;

use App\Filament\Resources\CropSeasons\CropSeasonResource;
use Filament\Resources\Pages\EditRecord;

class EditCropSeason extends EditRecord
{
    protected static string $resource = CropSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
