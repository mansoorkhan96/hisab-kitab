<?php

namespace App\Filament\Resources\CalculationResource\Pages;

use App\Filament\Resources\Calculations\CalculationResource;
use App\Models\Calculation;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditCalculation extends EditRecord
{
    protected static string $resource = CalculationResource::class;

    protected $listeners = [
        '$refresh' => '$refresh',
    ];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->icon(Heroicon::OutlinedPrinter)
                ->url(route('calculation.print', $this->record))
                ->openUrlInNewTab(),
            Action::make('save_form')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.save.label'))
                ->action(function (Calculation $record) {
                    $this->save(shouldSendSavedNotification: true);

                    $this->dispatch('$refresh');
                }),
        ];
    }

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->after(fn () => $this->dispatch('$refresh'));
    }
}
