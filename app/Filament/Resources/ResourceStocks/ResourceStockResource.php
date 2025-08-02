<?php

namespace App\Filament\Resources\ResourceStocks;

use App\Filament\Resources\ResourceStocks\Pages\CreateResourceStock;
use App\Filament\Resources\ResourceStocks\Pages\EditResourceStock;
use App\Filament\Resources\ResourceStocks\Pages\ListResourceStocks;
use App\Filament\Resources\ResourceStocks\Schemas\ResourceStockForm;
use App\Filament\Resources\ResourceStocks\Tables\ResourceStocksTable;
use App\Models\ResourceStock;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ResourceStockResource extends Resource
{
    protected static ?string $model = ResourceStock::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingStorefront;

    public static function form(Schema $schema): Schema
    {
        return ResourceStockForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ResourceStocksTable::configure($table);
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
            'index' => ListResourceStocks::route('/'),
            'create' => CreateResourceStock::route('/create'),
            'edit' => EditResourceStock::route('/{record}/edit'),
        ];
    }
}
