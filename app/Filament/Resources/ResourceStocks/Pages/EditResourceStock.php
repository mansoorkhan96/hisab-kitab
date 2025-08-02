<?php

namespace App\Filament\Resources\ResourceStocks\Pages;

use App\Filament\Resources\ResourceStocks\ResourceStockResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditResourceStock extends EditRecord
{
    protected static string $resource = ResourceStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
