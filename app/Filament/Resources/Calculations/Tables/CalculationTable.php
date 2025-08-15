<?php

namespace App\Filament\Resources\Calculations\Tables;

use App\Filament\Tables\Filters\CropSeasonFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CalculationTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Farmer')
                    ->searchable(),
                TextColumn::make('cropSeason.title')
                    ->searchable(),
                TextColumn::make('landlord_net_income')
                    ->label('Landlord Net Income')
                    ->money('PKR')
                    ->searchable()
                    ->sortable()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->summarize(Sum::make()->label('Total')->money('PKR')),
                TextColumn::make('farmer_revenue')
                    ->label('Farmer Profit/Loss')
                    ->money('PKR')
                    ->searchable()
                    ->sortable()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
            ])
            ->filters([
                CropSeasonFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
