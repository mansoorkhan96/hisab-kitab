<?php

namespace App\Filament\Resources\TractorResource\Pages;

use App\Filament\Resources\TractorResource;
use App\Filament\Widgets\TractorStatsWidget;
use Filament\Actions\DeleteAction;
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

    protected function getHeaderWidgets(): array
    {
        return [
            TractorStatsWidget::make([
                'record' => $this->getRecord(),
            ]),
        ];
    }
}
