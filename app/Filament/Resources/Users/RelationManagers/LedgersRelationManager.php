<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Enums\FarmingResourceType;
use App\Filament\Schemas\Components\CropSeasonSelect;
use App\Filament\Tables\Filters\CropSeasonFilter;
use App\Models\FarmingResource;
use App\Models\Ledger;
use App\Models\Tractor;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;

class LedgersRelationManager extends RelationManager
{
    protected static string $relationship = 'ledgers';

    protected static string|\BackedEnum|null $icon = 'heroicon-o-book-open';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CropSeasonSelect::make(),
                Select::make('farming_resource_id')
                    ->relationship('farmingResource', 'title')
                    ->live()
                    ->afterStateUpdated(function (Select $component, Set $set, Get $get) {
                        if (empty($get('rate'))) {
                            $set('rate', FarmingResource::find($component->getState())?->rate);
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('tractor_id')
                    ->live()
                    ->visible(function (Get $get) {
                        $farmingResource = FarmingResource::find($get('farming_resource_id'));

                        return $farmingResource?->type === FarmingResourceType::Implement;
                    })
                    ->relationship('tractor', 'title')
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

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('farmingResource.title')
                    ->searchable()
                    ->suffix(fn (Ledger $record) => ' ('.$record->farmingResource->type->name.')'),
                TextColumn::make('tractor.title')
                    ->searchable(),
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
                CropSeasonFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([$this->getCreateAction()])
            ->emptyStateActions([$this->getCreateAction()]);
    }

    protected function getCreateAction(): Action
    {
        return Action::make('Add new')
            ->hidden(fn (self $livewire) => $livewire->getOwnerRecord() instanceof Tractor)
            ->schema(
                fn (Schema $schema) => $this
                    ->form($schema)
                    ->columns(2)
            )
            ->action(function (array $data) {
                $this->ownerRecord->ledgers()->create($data);

                Notification::make()->success()->body('Ledger was created!');
            });
    }
}
