<?php

namespace App\Filament\Resources\FarmerResource\RelationManagers;

use App\Filament\Resources\FarmerLoanResource;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class FarmerLoansRelationManager extends RelationManager
{
    protected static string $relationship = 'farmerLoans';

    protected static ?string $icon = 'heroicon-o-banknotes';

    public function form(Form $form): Form
    {
        return FarmerLoanResource::form($form);
    }

    public function table(Table $table): Table
    {
        return FarmerLoanResource::table($table)
            ->headerActions([$this->getCreateAction()])
            ->emptyStateActions([$this->getCreateAction()])
            ->defaultSort('created_at', 'desc');
    }

    protected function getCreateAction(): Action
    {
        return Action::make('Add new')
            ->form(
                fn (Form $form) => $this
                    ->form($form)
                    ->columns(2)
            )
            ->action(function (array $data) {
                $this->ownerRecord->farmerLoans()->create($data);

                Notification::make()->success()->body('Farmer Loan was created!');
            });
    }
}
