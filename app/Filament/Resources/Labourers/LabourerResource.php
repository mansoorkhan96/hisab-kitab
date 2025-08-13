<?php

namespace App\Filament\Resources\Labourers;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Labourers\Pages\CreateLabourer;
use App\Filament\Resources\Labourers\Pages\EditLabourer;
use App\Filament\Resources\Labourers\Pages\ListLabourers;
use App\Filament\Resources\Labourers\Schemas\LabourerForm;
use App\Filament\Resources\Labourers\Tables\LabourersTable;
use App\Models\Labourer;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LabourerResource extends Resource
{
    protected static ?string $model = Labourer::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = NavigationGroup::CottonCrop;

    public static function form(Schema $schema): Schema
    {
        return LabourerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LabourersTable::configure($table);
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
            'index' => ListLabourers::route('/'),
            'create' => CreateLabourer::route('/create'),
            'edit' => EditLabourer::route('/{record}/edit'),
        ];
    }
}
