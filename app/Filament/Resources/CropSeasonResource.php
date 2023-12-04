<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CropSeasonResource\Pages;
use App\Models\CropSeason;
use App\Models\FarmingResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CropSeasonResource extends Resource
{
    protected static ?string $model = CropSeason::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required(),

                    // Repeater::make('rates')
                    //     ->columnSpanFull()
                    //     ->columns(2)
                    //     ->default(
                    //         fn () => auth()->user()
                    //             ?->farmingResources
                    //             ?->map(fn (FarmingResource $farmingResource) => [
                    //                 'farming_resource_id' => $farmingResource->id,
                    //                 'rate' => 0,
                    //             ])
                    //     )
                    //     ->schema([
                    //         Select::make('farming_resource_id')
                    //             ->label('Implement / Fertilizer / Seed')
                    //             ->options(FarmingResource::whereUserId(auth()->id())->pluck('name', 'id'))
                    //             ->required(),

                    //         TextInput::make('rate')
                    //             ->numeric()
                    //             ->required(),
                    //     ]),
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
