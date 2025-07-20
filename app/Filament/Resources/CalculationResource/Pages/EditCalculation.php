<?php

namespace App\Filament\Resources\CalculationResource\Pages;

use App\Filament\Resources\CalculationResource;
use App\Models\Calculation;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCalculation extends EditRecord
{
    protected static string $resource = CalculationResource::class;

    protected $listeners = [
        '$refresh' => '$refresh',
    ];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save_form')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action(function (Calculation $record) {
                    $this->save(shouldSendSavedNotification: true);
                }),
            DeleteAction::make(),
        ];
    }
}
