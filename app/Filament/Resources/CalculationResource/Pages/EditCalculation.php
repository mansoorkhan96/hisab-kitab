<?php

namespace App\Filament\Resources\CalculationResource\Pages;

use App\Filament\Resources\CalculationResource;
use App\Models\Calculation;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCalculation extends EditRecord
{
    protected static string $resource = CalculationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('save_form')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action(function (Calculation $record) {
                    $this->save(shouldSendSavedNotification: true);
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
