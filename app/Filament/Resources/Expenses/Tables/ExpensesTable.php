<?php

namespace App\Filament\Resources\Expenses\Tables;

use App\Filament\Tables\Filters\CropSeasonFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExpensesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('cropSeason.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('expensable.title')
                    ->hidden(fn ($livewire) => $livewire instanceof RelationManager)
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('PKR')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total')
                        ->money('PKR')
                    ),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('details')
                    ->limit(50)
                    ->html()
                    ->wrap(),
            ])
            ->filters([
                CropSeasonFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
