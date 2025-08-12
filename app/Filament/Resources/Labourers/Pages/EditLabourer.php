<?php

namespace App\Filament\Resources\Labourers\Pages;

use App\Filament\Resources\Labourers\LabourerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLabourer extends EditRecord
{
    protected static string $resource = LabourerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
