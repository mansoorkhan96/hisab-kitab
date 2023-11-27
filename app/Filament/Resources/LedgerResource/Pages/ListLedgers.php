<?php

namespace App\Filament\Resources\LedgerResource\Pages;

use App\Filament\Resources\LedgerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLedgers extends ListRecords
{
    protected static string $resource = LedgerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
