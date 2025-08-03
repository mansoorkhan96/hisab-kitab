<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Filament\Schemas\Components\CropSeasonSelect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CropSeasonSelect::make(),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('PKR'),
                DatePicker::make('date')
                    ->required()
                    ->default(now()),
                Textarea::make('details')
                    ->columnSpanFull()
                    ->rows(3)
                    ->maxLength(1000),
            ]);
    }
}
