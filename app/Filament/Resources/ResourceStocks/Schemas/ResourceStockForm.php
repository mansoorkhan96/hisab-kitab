<?php

namespace App\Filament\Resources\ResourceStocks\Schemas;

use App\Enums\FarmingResourceType;
use App\Filament\Resources\ResourceStocks\Pages\CreateResourceStock;
use App\Filament\Resources\ResourceStocks\Pages\EditResourceStock;
use App\Filament\Schemas\Components\CropSeasonSelect;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ResourceStockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                CropSeasonSelect::make(),
                Select::make('farming_resource_id')
                    ->visible(fn ($livewire) => $livewire instanceof EditResourceStock || $livewire instanceof CreateResourceStock)
                    ->relationship(
                        'farmingResource',
                        'title',
                        fn ($query) => $query->where('type', '!=', FarmingResourceType::Implement)
                    )
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->prefix('PKR'),
                DatePicker::make('date')
                    ->default(now())
                    ->required(),
                TextInput::make('supplier')
                    ->maxLength(255),
            ]);
    }
}
