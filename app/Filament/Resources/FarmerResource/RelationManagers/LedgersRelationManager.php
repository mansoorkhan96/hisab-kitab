<?php

namespace App\Filament\Resources\FarmerResource\RelationManagers;

use App\Filament\Resources\LedgerResource;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;

class LedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgers';

    protected static ?string $icon = 'heroicon-o-book-open';

    public function form(Form $form): Form
    {
        return LedgerResource::form($form);
    }

    public function table(Table $table): Table
    {
        return LedgerResource::table($table)
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
                $this->ownerRecord->ledgers()->create($data);

                Notification::make()->success()->body('Ledger was created!');
            });
    }
}
