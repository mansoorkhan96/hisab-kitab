<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CropSeasonResource\Pages;
use App\Models\CropSeason;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class CropSeasonResource extends Resource
{
    protected static ?string $model = CropSeason::class;

    protected static ?string $navigationIcon = 'heroicon-o-sun';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListCropSeasons::route('/'),
            'create' => Pages\CreateCropSeason::route('/create'),
            'edit' => Pages\EditCropSeason::route('/{record}/edit'),
        ];
    }
}
