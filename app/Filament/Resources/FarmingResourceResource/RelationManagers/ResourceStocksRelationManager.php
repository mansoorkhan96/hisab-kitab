<?php

namespace App\Filament\Resources\FarmingResourceResource\RelationManagers;

use App\Enums\FarmingResourceType;
use App\Filament\Schemas\Components\CropSeasonSelect;
use App\Filament\Tables\Filters\CropSeasonFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ResourceStocksRelationManager extends RelationManager
{
    protected static string $relationship = 'resourceStocks';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type !== FarmingResourceType::Implement;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CropSeasonSelect::make(),
                TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->step(0.01)
                    ->prefix('PKR'),
                DatePicker::make('date')
                    ->required()
                    ->default(now()),
                TextInput::make('supplier')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('quantity')
            ->columns([
                TextColumn::make('cropSeason.title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total')
                        ->numeric(2)
                    ),
                TextColumn::make('amount')
                    ->money('PKR')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total')
                        ->money('PKR')
                    ),
                TextColumn::make('date')
                    ->date()
                    ->sortable(),
                TextColumn::make('supplier')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                CropSeasonFilter::make(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('date', 'desc');
    }
}
