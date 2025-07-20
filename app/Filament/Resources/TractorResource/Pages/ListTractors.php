<?php

namespace App\Filament\Resources\TractorResource\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\TractorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTractors extends ListRecords
{
    protected static string $resource = TractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
