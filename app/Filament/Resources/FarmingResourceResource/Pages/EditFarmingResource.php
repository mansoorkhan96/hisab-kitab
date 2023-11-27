<?php

namespace App\Filament\Resources\FarmingResourceResource\Pages;

use App\Filament\Resources\FarmingResourceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFarmingResource extends EditRecord
{
    protected static string $resource = FarmingResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
