<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;

class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';

    protected static string | \BackedEnum | null $icon = 'heroicon-o-banknotes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('Rs.')
                    ->required(),
                Textarea::make('purpose')
                    ->required()
                    ->rows(3),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->money('PKR')
                    ->sortable(),
                TextColumn::make('purpose')
                    ->limit(50),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
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
                $this->ownerRecord->loans()->create($data);

                Notification::make()->success()->body('Loan was created!');
            });
    }
}