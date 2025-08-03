<?php

namespace App\Filament\Resources\Calculations;

use App\Filament\Resources\Calculations\Pages\CreateCalculation;
use App\Filament\Resources\Calculations\Pages\EditCalculation;
use App\Filament\Resources\Calculations\Pages\ListCalculations;
use App\Filament\Resources\Calculations\Schemas\CalculationForm;
use App\Filament\Resources\Calculations\Tables\CalculationTable;
use App\Models\Calculation;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CalculationResource extends Resource
{
    protected static ?string $model = Calculation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    public static function form(Schema $schema): Schema
    {
        return CalculationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CalculationTable::configure($table);
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
            'index' => ListCalculations::route('/'),
            'create' => CreateCalculation::route('/create'),
            'edit' => EditCalculation::route('/{record}/edit'),
        ];
    }
}