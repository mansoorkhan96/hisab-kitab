<?php

namespace App\Filament\Resources\CottonPickingRounds\Schemas;

use App\Filament\Schemas\Components\CropSeasonSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CottonPickingRoundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CropSeasonSelect::make(),
                Select::make('user_id')
                    ->label('Farmer')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label('Round Name')
                    ->required(),
            ]);
    }
}
