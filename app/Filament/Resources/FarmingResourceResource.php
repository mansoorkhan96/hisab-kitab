<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\FarmingResourceResource\Pages\ListFarmingResources;
use App\Filament\Resources\FarmingResourceResource\Pages\CreateFarmingResource;
use App\Filament\Resources\FarmingResourceResource\Pages\EditFarmingResource;
use App\Enums\FarmingResourceType;
use App\Enums\QuantityUnit;
use App\Filament\Resources\FarmingResourceResource\Pages;
use App\Models\FarmingResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmingResourceResource extends Resource
{
    protected static ?string $model = FarmingResource::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    // ->unique(ignoreRecord: true) TODO:
                    ->required(),
                Select::make('type')
                    ->options(FarmingResourceType::class)
                    ->required(),
                ToggleButtons::make('quantity_unit')
                    ->options(QuantityUnit::class)
                    ->inline()
                    ->required(),
                TextInput::make('rate')
                    ->prefixIcon('heroicon-o-banknotes')
                    ->numeric()
                    ->required()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('type')->searchable(),
                TextColumn::make('quantity_unit')->searchable(),
                TextColumn::make('rate')
                    ->money('PKR'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
            'index' => ListFarmingResources::route('/'),
            'create' => CreateFarmingResource::route('/create'),
            'edit' => EditFarmingResource::route('/{record}/edit'),
        ];
    }
}
