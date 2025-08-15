<?php

namespace App\Filament\Resources\Calculations\Schemas;

use App\Enums\CropType;
use App\Enums\FarmingResourceType;
use App\Filament\Components\CottonCalculationInfolist;
use App\Filament\Components\WheatCalculationInfolist;
use App\Filament\Resources\Calculations\Pages\EditCalculation;
use App\Filament\Resources\Calculations\RelationManagers\ThreshingsRelationManager;
use App\Filament\Schemas\Components\CropSeasonSelect;
use App\Filament\Widgets\LedgersTableWidget;
use App\Filament\Widgets\LoanWidget;
use App\Models\Calculation;
use App\Models\CropSeason;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class CalculationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('tabs')
                    ->contained(true)
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Tab::make('Calculation')
                            ->schema([
                                CropSeasonSelect::make()
                                    ->live()
                                    ->relationship(
                                        'cropSeason',
                                        'title',
                                        fn (Builder $query) => $query->whereNotNull('wheat_rate')
                                    )
                                    ->helperText('Set Wheat Rate to Crop Season to calculate'),
                                Select::make('user_id')
                                    ->label('Farmer')
                                    ->live()
                                    ->relationship('user', 'name')
                                    ->preload()
                                    ->required(),
                                ToggleButtons::make('crop_type')
                                    ->label('Crop Type')
                                    ->live()
                                    ->inline()
                                    ->options(CropType::class)
                                    ->required(),
                                TextInput::make('kudhi_in_kgs')
                                    ->label('Kudhi (KGs)')
                                    ->visible(fn (Get $get) => $get('crop_type') === CropType::Wheat->value)
                                    ->prefixIcon('heroicon-m-scale')
                                    ->live()
                                    ->numeric(),
                                TextInput::make('wheat_straw_rate')
                                    ->label('Wheat Straw Rate')
                                    ->visible(fn (Get $get) => $get('crop_type') === CropType::Wheat->value)
                                    ->live()
                                    ->minValue(0)
                                    ->default(fn () => CropSeason::current()->wheat_straw_rate)
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->numeric(),
                                TextInput::make('kamdari')
                                    ->label(
                                        fn (Get $get) => $get('crop_type') === CropType::Wheat->value
                                            ? 'Kamdari (KGs)'
                                            : 'Kamdari (PKR)'
                                    )
                                    ->prefixIcon(
                                        fn (Get $get) => $get('crop_type') === CropType::Wheat->value
                                            ? Heroicon::OutlinedScale
                                            : Heroicon::OutlinedBanknotes
                                    )
                                    ->numeric()
                                    ->live(),
                                Toggle::make('show_details')
                                    ->live()
                                    ->dehydrated(false)
                                    ->visible(fn (string $operation) => $operation === 'edit'),
                                Flex::make([
                                    Section::make('Calculation')
                                        ->schema([
                                            Livewire::make(
                                                fn (Calculation $calculation) => $calculation->crop_type === CropType::Wheat
                                                    ? WheatCalculationInfolist::class
                                                    : CottonCalculationInfolist::class,
                                                fn (Calculation $calculation) => [
                                                    'calculation' => $calculation,
                                                ]
                                            )
                                                ->key('Calculation-Infolist')
                                                ->visible(fn (string $context) => $context === 'edit'),
                                        ]),
                                    Group::make([
                                        Livewire::make(LedgersTableWidget::class, fn (EditCalculation $livewire, Get $get) => [
                                            'user_id' => $livewire->getRecord()->user_id,
                                            'crop_season_id' => $get('crop_season_id'),
                                            'farmingResourceTypes' => [FarmingResourceType::Fertilizer, FarmingResourceType::Pesticide],
                                            'tableHeading' => 'Dawa & Color',
                                            'groupsOnly' => ! $get('show_details'),
                                        ])->key('dawa-color'),
                                        Livewire::make(LedgersTableWidget::class, fn (EditCalculation $livewire, Get $get) => [
                                            'user_id' => $livewire->getRecord()->user_id,
                                            'crop_season_id' => $get('crop_season_id'),
                                            'farmingResourceTypes' => [FarmingResourceType::Implement, FarmingResourceType::Seed],
                                            'tableHeading' => 'Harr & Bij',
                                            'groupsOnly' => ! $get('show_details'),
                                        ])->key('harr-bij'),
                                        Livewire::make(
                                            LoanWidget::class,
                                            fn (EditCalculation $livewire) => ['record' => $livewire->getRecord()->user]
                                        )
                                            ->visible(fn (string $context) => $context === 'edit')
                                            ->key('farmer-loan'),
                                    ]),
                                ])
                                    ->visible(fn (string $context) => $context === 'edit')
                                    ->from('md')
                                    ->columnSpanFull(),
                            ]),
                        Tab::make('Threshors')
                            ->visible(fn (string $context, ?Calculation $calculation) => $context === 'edit' && $calculation->crop_type === CropType::Wheat)
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
}
