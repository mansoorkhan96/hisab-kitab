<?php

namespace App\Filament\Resources\ResourceStocks\Pages;

use App\Filament\Resources\ResourceStocks\ResourceStockResource;
use App\Filament\Widgets\ResourceStockChart;
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

    protected function getHeaderWidgets(): array
    {
        return [
            ResourceStockChart::class,
        ];
    }
}
