<?php

namespace App\Filament\Resources\CropSeasons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class CropSeasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule) {
                            return $rule->where('team_id', auth()->user()->team_id);
                        }
                    )
                    ->required(),
                Toggle::make('is_current')
                    ->label('Is Current Season')
                    ->default(true),
                TextInput::make('wheat_rate')
                    ->numeric(),
                TextInput::make('wheat_straw_rate')
                    ->numeric(),
                TextInput::make('cotton_rate_per_kg')
                    ->numeric(),
                TextInput::make('cotton_labour_rate_per_kg')
                    ->numeric(),
            ]);
    }
}
