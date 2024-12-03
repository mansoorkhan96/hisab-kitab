<?php

namespace App\Filament\Resources\CalculationResource\Pages;

use App\Filament\Resources\CalculationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalculation extends EditRecord
{
    protected static string $resource = CalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
