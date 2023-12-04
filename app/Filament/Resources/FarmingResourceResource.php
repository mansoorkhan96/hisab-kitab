<?php

namespace App\Filament\Resources;

use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Filament\Resources\FarmingResourceResource\Pages;
use App\Models\FarmingResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmingResourceResource extends Resource
{
    protected static ?string $model = FarmingResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->unique(ignoreRecord: true)->required(),
                Select::make('type')->options(FarmingResourceType::class)->required(),
                Select::make('quantity_unit')->options(QuantityUnit::class)->required(),
                TextInput::make('rate')->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->searchable(),
                TextColumn::make('quantity_unit')->searchable(),
                TextColumn::make('rate'),
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
            ])
            ->defaultSort('name');
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
            'index' => Pages\ListFarmingResources::route('/'),
            'create' => Pages\CreateFarmingResource::route('/create'),
            'edit' => Pages\EditFarmingResource::route('/{record}/edit'),
        ];
    }
}
