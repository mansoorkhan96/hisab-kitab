<?php

namespace App\Filament\Resources\CropSeasons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CropSeasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
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
}