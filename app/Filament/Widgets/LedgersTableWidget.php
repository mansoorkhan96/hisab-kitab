<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use App\Models\Ledger;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Reactive;

class LedgersTableWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    #[Reactive]
    public $farmer_id;

    #[Reactive]
    public $crop_season_id;

    public array $farmingResourceTypes = [];

    public string $tableHeading = 'Ledgers';

    #[Reactive]
    public $groupsOnly = true;

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->tableHeading)
            ->query(
                Ledger::query()
                    ->where('farmer_id', $this->farmer_id)
                    ->where('crop_season_id', $this->crop_season_id)
                    ->when(
                        filled($this->farmingResourceTypes),
                        fn (Builder $query) => $query->whereHas(
                            'farmingResource',
                            fn (Builder $query) => $query->whereIn('type', $this->farmingResourceTypes)
                        )
                    )
            )
            ->headerActions([
                Action::make('show_groups_only')
                    ->hidden()
                    ->label($this->groupsOnly ? 'Show details' : 'Hide Details')
                    ->action(function () {
                        $this->groupsOnly = ! $this->groupsOnly;

                        $this->dispatch('$refresh');
                    }),
            ])
            ->columns([
                TextColumn::make('farmingResource.name')
                    ->label('Item'),
                TextColumn::make('quantity')
                    ->numeric()
                    ->suffix(fn (Ledger $record) => $record->quantity_with_unit)
                    ->summarize(Sum::make()->label('Total')),
                TextInputColumn::make('rate')
                    ->hidden($this->groupsOnly)
                    ->default(fn (Ledger $ledger) => $ledger->farmingResource->rate),
                TextColumn::make('amount')
                    ->summarize(Sum::make()->label('Total')->money('PKR')),
            ])
            ->groups([
                Group::make('farmingResource.name')
                    ->titlePrefixedWithLabel(false),
            ])
            ->groupsOnly($this->groupsOnly)
            ->defaultGroup('farmingResource.name')
            ->paginated(false);
    }
}
