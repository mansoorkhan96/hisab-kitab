<?php

namespace App\Filament\Resources;

use App\Enums\FarmingResourceType;
use App\Filament\Components\CalculationInfolist;
use App\Filament\Resources\CalculationResource\Pages;
use App\Filament\Resources\CalculationResource\Pages\EditCalculation;
use App\Filament\Resources\CalculationResource\RelationManagers\ThreshingsRelationManager;
use App\Filament\Resources\FarmerResource\Widgets\LoanWidget;
use App\Filament\Widgets\LedgersTableWidget;
use App\Models\Calculation;
use App\Models\CropSeason;
use App\Models\Farmer;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Livewire;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CalculationResource extends Resource
{
    protected static ?string $model = Calculation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('tabs')
                    ->contained(true)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Tabs\Tab::make('Calculation')
                            ->schema([
                                Select::make('crop_season_id')
                                    ->live()
                                    ->relationship('cropSeason', 'name')
                                    ->default(CropSeason::where('is_current', true)->first()?->id)
                                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                                        if (empty($state)) {
                                            return;
                                        }

                                        $cropSeason = CropSeason::find($state);

                                        if (empty($get('wheat_rate'))) {
                                            $set('wheat_rate', $cropSeason?->wheat_rate);
                                        }

                                        if (empty($get('wheat_straw_rate'))) {
                                            $set('wheat_straw_rate', $cropSeason?->wheat_straw_rate);
                                        }
                                    })
                                    ->preload()
                                    ->required(),
                                Select::make('farmer_id')
                                    ->live()
                                    ->relationship('farmer', 'name')
                                    ->preload()
                                    ->required(),
                                TextInput::make('wheat_rate')
                                    ->live()
                                    ->required()
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->numeric(),
                                TextInput::make('kudhi_in_kgs')
                                    ->label('Kudhi (KGs)')
                                    ->prefixIcon('heroicon-m-scale')
                                    ->live()
                                    ->numeric(),
                                TextInput::make('wheat_straw_rate')
                                    ->live()
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->numeric(),
                                TextInput::make('kamdari_in_kgs')
                                    ->label('Kamdari (KGs)')
                                    ->prefixIcon('heroicon-m-scale')
                                    ->numeric()
                                    ->live(),
                                Toggle::make('show_details')
                                    ->live()
                                    ->dehydrated(false),
                                Split::make([
                                    Section::make('Calculation')
                                        ->schema([
                                            Placeholder::make('alert')
                                                ->disabled()
                                                ->label('Please save the form to see the calucation...')
                                                ->visible(fn (string $context) => $context === 'create'),
                                            Livewire::make(
                                                CalculationInfolist::class,
                                                fn (EditCalculation $livewire) => [
                                                    'calculation' => $livewire->getRecord(),
                                                ]
                                            )
                                                ->key('Calculation-Infolist')
                                                ->visible(fn (string $context) => $context === 'edit'),
                                        ]),
                                    Group::make([
                                        Livewire::make(LedgersTableWidget::class, fn (Get $get) => [
                                            'farmer_id' => $get('farmer_id'),
                                            'crop_season_id' => $get('crop_season_id'),
                                            'farmingResourceTypes' => [FarmingResourceType::Fertilizer, FarmingResourceType::Pesticide],
                                            'tableHeading' => 'Dawa & Color',
                                            'groupsOnly' => ! $get('show_details'),
                                        ])->key('dawa-color'),
                                        Livewire::make(LedgersTableWidget::class, fn (Get $get) => [
                                            'farmer_id' => $get('farmer_id'),
                                            'crop_season_id' => $get('crop_season_id'),
                                            'farmingResourceTypes' => [FarmingResourceType::Implement, FarmingResourceType::Seed],
                                            'tableHeading' => 'Harr & Bij',
                                            'groupsOnly' => ! $get('show_details'),
                                        ])->key('harr-bij'),
                                        Livewire::make(
                                            LoanWidget::class,
                                            fn (Get $get) => ['record' => Farmer::find($get('farmer_id'))]
                                        )
                                            ->visible(fn (string $context) => $context === 'edit')
                                            ->key('farmer-loan'),
                                    ]),
                                ])
                                    ->from('md')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Threshors')
                            ->visible(fn (string $context) => $context === 'edit')
                            ->columnSpanFull()
                            ->schema([
                                Livewire::make(ThreshingsRelationManager::class, fn (EditCalculation $livewire) => [
                                    'ownerRecord' => $livewire->getRecord(),
                                    'pageClass' => EditCalculation::class,
                                ])
                                    ->key('threshings')
                                    ->columnSpanFull(),
                            ]),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('farmer.name')
                    ->searchable(),
                TextColumn::make('cropSeason.name')
                    ->searchable(),
                // TODO:
                // status (loss/profit) red/green
                // amount red/green
            ])
            ->filters([
                SelectFilter::make('crop_season_id')
                    ->label('Crop Season')
                    ->default(CropSeason::where('is_current', true)->first()?->id)
                    ->options(CropSeason::all()->pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListCalculations::route('/'),
            'create' => Pages\CreateCalculation::route('/create'),
            'edit' => Pages\EditCalculation::route('/{record}/edit'),
        ];
    }
}
