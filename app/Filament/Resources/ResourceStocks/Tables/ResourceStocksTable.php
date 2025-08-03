<?php

namespace App\Filament\Resources\ResourceStocks\Tables;

use App\Filament\Resources\ResourceStocks\Pages\ListResourceStocks;
use App\Filament\Tables\Filters\CropSeasonFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ResourceStocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('farmingResource.title')
                    ->visible(fn ($livewire) => $livewire instanceof ListResourceStocks)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total')
                        ->numeric(2)
                    ),
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
                TextColumn::make('supplier')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
