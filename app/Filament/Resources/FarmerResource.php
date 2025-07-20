<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\FarmerResource\Pages\ListFarmers;
use App\Filament\Resources\FarmerResource\Pages\CreateFarmer;
use App\Filament\Resources\FarmerResource\Pages\EditFarmer;
use App\Filament\Resources\FarmerResource\Pages;
use App\Filament\Resources\FarmerResource\RelationManagers\FarmerLoansRelationManager;
use App\Filament\Resources\FarmerResource\RelationManagers\LedgersRelationManager;
use App\Filament\Resources\FarmerResource\RelationManagers\LoanPaymentsRelationManager;
use App\Models\Farmer;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FarmerResource extends Resource
{
    protected static ?string $model = Farmer::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->unique(ignoreRecord: true)->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
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
            LedgersRelationManager::class,
            FarmerLoansRelationManager::class,
            LoanPaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFarmers::route('/'),
            'create' => CreateFarmer::route('/create'),
            'edit' => EditFarmer::route('/{record}/edit'),
        ];
    }
}
