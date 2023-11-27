<?php

namespace App\Filament\Resources\FarmerResource\RelationManagers;

use App\Filament\Resources\LedgerResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class LedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgers';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return LedgerResource::table($table);
    }
}
