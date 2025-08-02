<?php

namespace App\Filament\Resources\FarmingResourceResource\RelationManagers;

use App\Enums\FarmingResourceType;
use App\Filament\Resources\ResourceStocks\Schemas\ResourceStockForm;
use App\Filament\Resources\ResourceStocks\Tables\ResourceStocksTable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ResourceStocksRelationManager extends RelationManager
{
    protected static string $relationship = 'resourceStocks';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type !== FarmingResourceType::Implement;
    }

    public function form(Schema $schema): Schema
    {
        return ResourceStockForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ResourceStocksTable::configure($table);
    }
}
