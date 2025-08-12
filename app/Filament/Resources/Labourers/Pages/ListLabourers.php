<?php

namespace App\Filament\Resources\Labourers\Pages;

use App\Filament\Resources\Labourers\LabourerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLabourers extends ListRecords
{
    protected static string $resource = LabourerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
