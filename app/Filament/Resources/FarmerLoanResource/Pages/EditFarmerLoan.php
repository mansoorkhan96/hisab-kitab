<?php

namespace App\Filament\Resources\FarmerLoanResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\FarmerLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFarmerLoan extends EditRecord
{
    protected static string $resource = FarmerLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
