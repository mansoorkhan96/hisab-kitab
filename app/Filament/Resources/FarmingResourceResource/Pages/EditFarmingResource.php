<?php

namespace App\Filament\Resources\FarmingResourceResource\Pages;

use App\Filament\Resources\FarmingResourceResource;
use App\Filament\Widgets\ResourceStockOverview;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFarmingResource extends EditRecord
{
    protected static string $resource = FarmingResourceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ResourceStockOverview::make([
                'farmingResource' => $this->getRecord(),
            ]),
        ];
    }
}
