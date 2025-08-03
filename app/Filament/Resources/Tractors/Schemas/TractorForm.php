<?php

namespace App\Filament\Resources\Tractors\Schemas;

use App\Enums\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TractorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('user_id')
                    ->label('Driver')
                    ->relationship('user', 'name', fn (Builder $query) => $query->where('role', Role::Driver))
                    ->required()
                    ->searchable()
                    ->preload(),
            ]);
    }
}