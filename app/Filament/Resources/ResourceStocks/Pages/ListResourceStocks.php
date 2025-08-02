<?php

namespace App\Filament\Resources\ResourceStocks\Pages;

use App\Filament\Resources\ResourceStocks\ResourceStockResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListResourceStocks extends ListRecords
{
    protected static string $resource = ResourceStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
