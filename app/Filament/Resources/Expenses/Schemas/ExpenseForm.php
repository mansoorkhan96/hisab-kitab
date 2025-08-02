<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Models\CropSeason;
use App\Models\Tractor;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MorphToSelect;
use App\Filament\Schemas\Components\CropSeasonSelect;
use Filament\Forms\Components\Select;
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
                // MorphToSelect::make('expensable')
                //     ->live()
                //     ->types([
                //         MorphToSelect\Type::make(Tractor::class)
                //             ->titleAttribute('title'),
                //     ]),
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
