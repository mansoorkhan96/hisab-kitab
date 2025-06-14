<?php

namespace App\Filament\Resources\FarmerResource\Pages;

use App\Filament\Resources\FarmerResource;
use App\Filament\Resources\FarmerResource\Widgets\LoanWidget;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFarmer extends EditRecord
{
    protected static string $resource = FarmerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LoanWidget::make(['columns' => 3]),
        ];
    }

    protected function getWidgets(): array
    {
        return [
            LoanWidget::make([
                'farmer' => $this->getRecord(),
            ]),
        ];
    }
}
