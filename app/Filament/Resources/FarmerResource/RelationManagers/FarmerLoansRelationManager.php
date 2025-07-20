<?php

namespace App\Filament\Resources\FarmerResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use App\Filament\Resources\FarmerLoanResource;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class FarmerLoansRelationManager extends RelationManager
{
    protected static string $relationship = 'farmerLoans';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-banknotes';

    public function form(Schema $schema): Schema
    {
        return FarmerLoanResource::form($schema);
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
            ->schema(
                fn (Schema $schema) => $this
                    ->form($schema)
                    ->columns(2)
            )
            ->action(function (array $data) {
                $this->ownerRecord->farmerLoans()->create($data);

                Notification::make()->success()->body('Farmer Loan was created!');
            });
    }
}
