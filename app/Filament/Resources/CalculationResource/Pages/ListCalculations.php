<?php

namespace App\Filament\Resources\CalculationResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\CalculationResource;
use Filament\Actions;
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
