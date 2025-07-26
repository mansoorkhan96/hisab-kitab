<?php

namespace App\Filament\Resources\CalculationResource\RelationManagers;

use App\Filament\Resources\TractorResource\Pages\EditTractor;
use App\Helpers\Converter;
use App\Models\CropSeason;
use App\Models\Threshing;
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
use Filament\Tables\Filters\SelectFilter;
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
                TextColumn::make('tractor.title'),
                TextColumn::make('total_wheat_sacks')
                    ->label('Batai')
                    ->suffix(' Bori')
                    ->sortable()
                    ->summarize(Sum::make()
                        ->label('Total Batai')
                        ->visible(fn (HasTable $livewire) => filled(Arr::get($livewire->getTableFilterState('calculation.crop_season_id'), 'value')))
                        ->formatStateUsing(function ($state) {
                            $totalBataiInKgs = $state * 100;

                            return Converter::kgsToSacksString($totalBataiInKgs);
                        }),
                    ),
                TextColumn::make('charges')
                    ->label('Charges')
                    ->getStateUsing(function (Threshing $record) {
                        $thresherInKgs = $record->total_wheat_sacks * 10;

                        return Converter::kgsToSacksString($thresherInKgs);
                    })
                    ->summarize(Summarizer::make()
                        ->label('Total Charges')
                        ->visible(fn (HasTable $livewire) => filled(Arr::get($livewire->getTableFilterState('calculation.crop_season_id'), 'value')))
                        ->using(function (Builder $query) {
                            $thresherInKgs = $query->sum('total_wheat_sacks') * 10;

                            return Converter::kgsToSacksString($thresherInKgs);
                        }),
                    ),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->getStateUsing(function (Threshing $record) {
                        $thresherInKgs = $record->total_wheat_sacks * 10;

                        return ($thresherInKgs / 100) * $record->calculation->wheat_rate;
                    })
                    ->money('PKR')
                    ->summarize(Summarizer::make()
                        ->label('Total Amount')
                        ->visible(fn (HasTable $livewire) => filled(Arr::get($livewire->getTableFilterState('calculation.crop_season_id'), 'value')))
                        ->using(function (Builder $query, HasTable $livewire) {
                            $thresherInKgs = $query->sum('total_wheat_sacks') * 10;

                            $cropSeasonId = Arr::get($livewire->getTableFilterState('calculation.crop_season_id'), 'value');

                            $cropSeason = CropSeason::find($cropSeasonId);

                            if (blank($cropSeason->wheat_rate)) {
                                return 'Please set Wheat Rate in Crop Season to calculate amount';
                            }

                            return ($thresherInKgs / 100) * $cropSeason->wheat_rate;
                        })
                        ->money('PKR'),
                    ),
            ])
            ->filters([
                // TODO: use relationship()
                SelectFilter::make('calculation.crop_season_id')
                    ->label('Crop Season')
                    ->options(CropSeason::all()->pluck('title', 'id'))
                    ->default(CropSeason::where('is_current', true)->first()?->id)
                    ->query(function (EloquentBuilder $query, $data) {
                        return $query
                            ->when($data['value'], function (EloquentBuilder $query) use ($data) {
                                $query->whereHas('calculation', function (EloquentBuilder $query) use ($data) {
                                    $query->where('crop_season_id', $data['value']);
                                });
                            });
                    }),
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
