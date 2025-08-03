<?php

namespace App\Filament\Resources\Tractors;

use App\Filament\Resources\Calculations\RelationManagers\ThreshingsRelationManager;
use App\Filament\Resources\Tractors\Pages\CreateTractor;
use App\Filament\Resources\Tractors\Pages\EditTractor;
use App\Filament\Resources\Tractors\Pages\ListTractors;
use App\Filament\Resources\Tractors\RelationManagers\ExpensesRelationManager;
use App\Filament\Resources\Tractors\Schemas\TractorForm;
use App\Filament\Resources\Tractors\Tables\TractorTable;
use App\Filament\Resources\Users\RelationManagers\LedgersRelationManager;
use App\Models\Tractor;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TractorResource extends Resource
{
    protected static ?string $model = Tractor::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    public static function form(Schema $schema): Schema
    {
        return TractorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TractorTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LedgersRelationManager::class,
            ThreshingsRelationManager::class,
            ExpensesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTractors::route('/'),
            'create' => CreateTractor::route('/create'),
            'edit' => EditTractor::route('/{record}/edit'),
        ];
    }
}