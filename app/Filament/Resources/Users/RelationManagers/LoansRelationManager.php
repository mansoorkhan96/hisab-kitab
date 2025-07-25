<?php

namespace App\Filament\Resources\Users\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LoansRelationManager extends RelationManager
{
    protected static string $relationship = 'loans';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-banknotes';

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
            ->headerActions([
                CreateAction::make()
                    ->label('Add Loan')
                    ->after(fn (self $livewire) => $livewire->dispatch('$refresh')),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
