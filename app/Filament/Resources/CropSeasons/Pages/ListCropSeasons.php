<?php

namespace App\Filament\Resources\CropSeasons\Pages;

use App\Filament\Resources\CropSeasons\CropSeasonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCropSeasons extends ListRecords
{
    protected static string $resource = CropSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
