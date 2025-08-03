<?php

namespace App\Filament\Resources\CropSeasons;

use App\Filament\Resources\CropSeasons\Pages\CreateCropSeason;
use App\Filament\Resources\CropSeasons\Pages\EditCropSeason;
use App\Filament\Resources\CropSeasons\Pages\ListCropSeasons;
use App\Filament\Resources\CropSeasons\Schemas\CropSeasonForm;
use App\Filament\Resources\CropSeasons\Tables\CropSeasonTable;
use App\Models\CropSeason;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CropSeasonResource extends Resource
{
    protected static ?string $model = CropSeason::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sun';

    public static function form(Schema $schema): Schema
    {
        return CropSeasonForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CropSeasonTable::configure($table);
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
            'index' => ListCropSeasons::route('/'),
            'create' => CreateCropSeason::route('/create'),
            'edit' => EditCropSeason::route('/{record}/edit'),
        ];
    }
}