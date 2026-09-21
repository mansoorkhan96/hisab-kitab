<?php

namespace App\Filament\Resources\FarmingResourceResource\Pages;

use App\Filament\Resources\FarmingResourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListFarmingResources extends ListRecords
{
    protected static string $resource = FarmingResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
