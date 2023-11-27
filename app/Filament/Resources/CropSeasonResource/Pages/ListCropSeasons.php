<?php

namespace App\Filament\Resources\CropSeasonResource\Pages;

use App\Filament\Resources\CropSeasonResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCropSeasons extends ListRecords
{
    protected static string $resource = CropSeasonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
