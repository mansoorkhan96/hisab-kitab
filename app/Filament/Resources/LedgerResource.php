<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LedgerResource\Pages;
use App\Models\Ledger;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LedgerResource extends Resource
{
    protected static ?string $model = Ledger::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('cropSeason.name')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                TextColumn::make('farmer.name')
                    ->visible((request()->routeIs('filament.admin.resources.ledgers.index')))
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(false),

                TextColumn::make('farmingResource.name')
                    ->searchable()
                    ->toggleable()
                    ->toggledHiddenByDefault(false)
                    ->suffix(fn (Ledger $record) => ' ('.$record->farmingResource->type->name.')'),

                TextColumn::make('quantity')
                    ->suffix(fn (Ledger $record) => $record->quantity > 1
                        ? ' '.str($record->farmingResource->quantity_unit)->plural()
                        : ' '.$record->farmingResource->quantity_unit
                    )
                    ->numeric(),

                TextColumn::make('rate')
                    ->numeric()
                    ->prefix('Rs '),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLedgers::route('/'),
            'create' => Pages\CreateLedger::route('/create'),
            'edit' => Pages\EditLedger::route('/{record}/edit'),
        ];
    }
}
