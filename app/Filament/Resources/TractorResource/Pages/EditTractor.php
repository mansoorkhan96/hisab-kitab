<?php

namespace App\Filament\Resources\TractorResource\Pages;

use Filament\Actions\DeleteAction;
use App\Filament\Resources\TractorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTractor extends EditRecord
{
    protected static string $resource = TractorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
