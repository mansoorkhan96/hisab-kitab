<?php

namespace App\Filament\Resources\Calculations\RelationManagers;

use App\Filament\Resources\TractorResource\Pages\EditTractor;
use App\Filament\Tables\Filters\CropSeasonFilter;
use App\Models\CropSeason;
use App\Models\Threshing;
use App\Models\Tractor;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class ThreshingsRelationManager extends RelationManager
{
    protected static string $relationship = 'threshings';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('tractor_id')
                    ->relationship('tractor', 'title')
                    ->required(),
                TextInput::make('total_wheat_sacks')
                    ->label('Batai')
                    ->required()
                    ->numeric()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tractor.title')
            ->modifyQueryUsing(function (EloquentBuilder $query) {
                return $query->with('calculation');
            })
            ->columns([
                TextColumn::make('tractor.title')
                    ->hidden(fn (self $livewire) => $livewire->getOwnerRecord() instanceof Tractor),
                TextColumn::make('total_wheat_sacks')
                    ->label('Batai')
                    ->suffix(' Bori')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Batai')
                        ->suffix(' Bori'),
                    ),
                TextColumn::make('threshing_charges_in_sacks')
                    ->label('Charges')
                    ->suffix(' Bori')
                    ->summarize(Sum::make()
                        ->label('Total Charges')
                        ->suffix(' Bori'),
                    ),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->getStateUsing(function (Threshing $record) {
                        $cropSeason = $record->calculation->cropSeason;

                        return $record->threshing_charges_in_sacks * $cropSeason->wheat_rate;
                    })
                    ->money('PKR')
                    ->summarize(Summarizer::make()
                        ->label('Total Amount')
                        ->visible(fn (HasTable $livewire) => filled(Arr::get($livewire->getTableFilterState('calculation.cropSeason'), 'value')))
                        ->using(function (Builder $query, HasTable $livewire) {
                            $cropSeasonId = Arr::get($livewire->getTableFilterState('calculation.cropSeason'), 'value');

                            $cropSeason = CropSeason::find($cropSeasonId);

                            if (blank($cropSeason->wheat_rate)) {
                                return 'Please set Wheat Rate in Crop Season to calculate amount';
                            }

                            return $query->sum('threshing_charges_in_sacks') * $cropSeason->wheat_rate;
                        })
                        ->money('PKR')
                        ->suffix(fn () => ' (Based on Wheat Rate in Crop Season)'),
                    ),
            ])
            ->filters([
                CropSeasonFilter::make('calculation.cropSeason'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->hidden(fn (self $livewire) => $livewire->pageClass === EditTractor::class),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
