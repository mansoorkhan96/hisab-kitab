<?php

namespace App\Filament\Resources\Labourers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LabourerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
