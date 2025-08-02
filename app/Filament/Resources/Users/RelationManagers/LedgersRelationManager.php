<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\LedgerResource;
use App\Models\Tractor;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class LedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgers';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-book-open';

    public function form(Schema $schema): Schema
    {
        return LedgerResource::form($schema);
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
            ->hidden(fn (self $livewire) => $livewire->getOwnerRecord() instanceof Tractor)
            ->schema(
                fn (Schema $schema) => $this
                    ->form($schema)
                    ->columns(2)
            )
            ->action(function (array $data) {
                $this->ownerRecord->ledgers()->create($data);

                Notification::make()->success()->body('Ledger was created!');
            });
    }
}
