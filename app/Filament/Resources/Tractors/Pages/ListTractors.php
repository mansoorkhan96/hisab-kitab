<?php

namespace App\Filament\Resources\Tractors\Pages;

use App\Filament\Resources\Tractors\TractorResource;
use Filament\Actions\CreateAction;
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
