<?php

namespace App\Filament\Resources\CropSeasonResource\Pages;

use App\Filament\Resources\CropSeasonResource;
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
