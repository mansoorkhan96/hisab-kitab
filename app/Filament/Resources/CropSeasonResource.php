<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\CropSeasonResource\Pages\ListCropSeasons;
use App\Filament\Resources\CropSeasonResource\Pages\CreateCropSeason;
use App\Filament\Resources\CropSeasonResource\Pages\EditCropSeason;
use App\Filament\Resources\CropSeasonResource\Pages;
use App\Models\CropSeason;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CropSeasonResource extends Resource
{
    protected static ?string $model = CropSeason::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-sun';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required(),
                Toggle::make('is_current')
                    ->label('Is Current Season')
                    ->default(true),
                TextInput::make('wheat_rate')
                    ->numeric(),
                TextInput::make('wheat_straw_rate')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable(),
                ToggleColumn::make('is_current'),
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
            ->defaultSort('name', 'desc');
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
