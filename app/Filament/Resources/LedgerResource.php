<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\LedgerResource\Pages\ListLedgers;
use App\Filament\Resources\LedgerResource\Pages\CreateLedger;
use App\Filament\Resources\LedgerResource\Pages\EditLedger;
use App\Filament\Resources\LedgerResource\Pages;
use App\Models\CropSeason;
use App\Models\FarmingResource;
use App\Models\Ledger;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class LedgerResource extends Resource
{
    protected static ?string $model = Ledger::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('crop_season_id')
                ->relationship('cropSeason', 'name')
                ->searchable()
                ->preload()
                ->default(
                    CropSeason::query()
                        ->where('user_id', auth()->id())
                        ->where('is_current', true)
                        ->first()
                        ->id
                )
                ->required(),
            // Select::make('farmer_id')
            //     ->relationship('farmer', 'name')
            //     ->searchable()
            //     ->preload()
            //     ->required(),
            Select::make('farming_resource_id')
                ->relationship('farmingResource', 'name')
                ->live()
                ->afterStateUpdated(function (Select $component, Set $set, Get $get) {
                    if (empty($get('rate'))) {
                        $set('rate', FarmingResource::find($component->getState())?->rate);
                    }
                })
                ->searchable()
                ->preload()
                ->required(),
            TextInput::make('quantity')
                ->default(1)
                ->numeric()
                ->required(),
            TextInput::make('rate')
                ->prefixIcon('heroicon-m-currency-dollar')
                ->numeric()
                ->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->groups([
                Group::make('farmingResource.name')
                    ->titlePrefixedWithLabel(false),
            ])
            ->defaultGroup('farmingResource.name')
            ->columns([
                TextColumn::make('cropSeason.name')
                    ->searchable(), // TODO: HIde and make it filter and default to current
                TextColumn::make('farmer.name')
                    ->visible((request()->routeIs('filament.admin.resources.ledgers.index')))
                    ->searchable(),
                TextColumn::make('farmingResource.name')
                    ->searchable()
                    ->suffix(fn (Ledger $record) => ' ('.$record->farmingResource->type->name.')'),
                TextColumn::make('quantity')
                    ->suffix(fn (Ledger $record) => $record->quantity_with_unit)
                    ->numeric()
                    ->summarize(Sum::make()->label('Total Qty')),
                TextInputColumn::make('rate')
                    ->default(fn (Ledger $ledger) => $ledger->farmingResource->rate),
                TextColumn::make('amount')
                    ->summarize(Sum::make()->label('Total')->money('PKR')),
            ])
            ->filters([
                SelectFilter::make('crop_season_id')
                    ->label('Crop Season')
                    ->default(CropSeason::where('is_current', true)->first()?->id)
                    ->options(CropSeason::all()->pluck('name', 'id')),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
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
            'index' => ListLedgers::route('/'),
            'create' => CreateLedger::route('/create'),
            'edit' => EditLedger::route('/{record}/edit'),
        ];
    }
}
