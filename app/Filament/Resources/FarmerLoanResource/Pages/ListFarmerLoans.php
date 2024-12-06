<?php

namespace App\Filament\Resources\FarmerLoanResource\Pages;

use App\Filament\Resources\FarmerLoanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFarmerLoans extends ListRecords
{
    protected static string $resource = FarmerLoanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
