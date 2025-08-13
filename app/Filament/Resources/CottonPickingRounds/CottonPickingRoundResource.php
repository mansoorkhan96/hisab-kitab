<?php

namespace App\Filament\Resources\CottonPickingRounds;

use App\Enums\NavigationGroup;
use App\Filament\Resources\CottonPickingRounds\Pages\CreateCottonPickingRound;
use App\Filament\Resources\CottonPickingRounds\Pages\EditCottonPickingRound;
use App\Filament\Resources\CottonPickingRounds\Pages\ListCottonPickingRounds;
use App\Filament\Resources\CottonPickingRounds\RelationManagers\CottonPickingDailiesRelationManager;
use App\Filament\Resources\CottonPickingRounds\Schemas\CottonPickingRoundForm;
use App\Filament\Resources\CottonPickingRounds\Tables\CottonPickingRoundsTable;
use App\Models\CottonPickingRound;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class CottonPickingRoundResource extends Resource
{
    protected static ?string $model = CottonPickingRound::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::CottonCrop;

    public static function form(Schema $schema): Schema
    {
        return CottonPickingRoundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CottonPickingRoundsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            CottonPickingDailiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCottonPickingRounds::route('/'),
            'create' => CreateCottonPickingRound::route('/create'),
            'edit' => EditCottonPickingRound::route('/{record}/edit'),
        ];
    }
}
