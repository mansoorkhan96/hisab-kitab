<?php

namespace App\Filament\Resources\TractorResource\RelationManagers;

use App\Filament\Resources\Expenses\Schemas\ExpenseForm;
use App\Filament\Resources\Expenses\Tables\ExpensesTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    public function form(Schema $schema): Schema
    {
        return ExpenseForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return ExpensesTable::configure($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
