<?php

namespace App\Filament\Resources\CottonPickingRounds\Tables;

use App\Filament\Tables\Filters\CropSeasonFilter;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class CottonPickingRoundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultGroup('user.name')
            ->columns([
                // TextColumn::make('user.name')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                CropSeasonFilter::make(),
            ])
            ->groups([
                Group::make('user.name')
                    ->label('Farmer'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
