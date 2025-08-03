<?php

namespace App\Filament\Resources\Calculations\Pages;

use App\Filament\Resources\Calculations\CalculationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCalculations extends ListRecords
{
    protected static string $resource = CalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
