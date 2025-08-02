<?php

namespace App\Filament\Resources;

use App\Enums\FarmingResourceType;
use App\Filament\Components\CalculationInfolist;
use App\Filament\Resources\CalculationResource\Pages\CreateCalculation;
use App\Filament\Resources\CalculationResource\Pages\EditCalculation;
use App\Filament\Resources\CalculationResource\Pages\ListCalculations;
use App\Filament\Resources\CalculationResource\RelationManagers\ThreshingsRelationManager;
use App\Filament\Schemas\Components\CropSeasonSelect;
use App\Filament\Tables\Filters\CropSeasonFilter;
use App\Filament\Widgets\LedgersTableWidget;
use App\Filament\Widgets\LoanWidget;
use App\Models\Calculation;
use App\Models\CropSeason;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CalculationResource extends Resource
{
    protected static ?string $model = Calculation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calculator';

    public static function form(Schema $schema): Schema
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
                                    }),
                                Select::make('user_id')
                                    ->live()
                                    ->relationship('user', 'name')
                                    ->preload()
                                    ->required(),
                                TextInput::make('wheat_rate')
                                    ->live()
                                    ->required()
                                    ->default(fn () => CropSeason::current()->wheat_rate)
                                    ->minValue(0)
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->numeric(),
                                TextInput::make('kudhi_in_kgs')
                                    ->label('Kudhi (KGs)')
                                    ->prefixIcon('heroicon-m-scale')
                                    ->live()
                                    ->numeric(),
                                TextInput::make('wheat_straw_rate')
                                    ->live()
                                    ->minValue(0)
                                    ->default(fn () => CropSeason::current()->wheat_straw_rate)
                                    ->prefixIcon('heroicon-m-banknotes')
                                    ->numeric(),
                                TextInput::make('kamdari_in_kgs')
                                    ->label('Kamdari (KGs)')
                                    ->prefixIcon('heroicon-m-scale')
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
                                            'user_id' => $get('user_id'),
                                            'crop_season_id' => $get('crop_season_id'),
                                            'farmingResourceTypes' => [FarmingResourceType::Fertilizer, FarmingResourceType::Pesticide],
                                            'tableHeading' => 'Dawa & Color',
                                            'groupsOnly' => ! $get('show_details'),
                                        ])->key('dawa-color'),
                                        Livewire::make(LedgersTableWidget::class, fn (Get $get) => [
                                            'user_id' => $get('user_id'),
                                            'crop_season_id' => $get('crop_season_id'),
                                            'farmingResourceTypes' => [FarmingResourceType::Implement, FarmingResourceType::Seed],
                                            'tableHeading' => 'Harr & Bij',
                                            'groupsOnly' => ! $get('show_details'),
                                        ])->key('harr-bij'),
                                        Livewire::make(
                                            LoanWidget::class,
                                            fn (Get $get) => ['record' => User::find($get('user_id'))]
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
                TextColumn::make('user.name')
                    ->searchable(),
                TextColumn::make('cropSeason.title')
                    ->searchable(),
                // TODO:
                // status (loss/profit) red/green
                // amount red/green
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
            'index' => ListCalculations::route('/'),
            'create' => CreateCalculation::route('/create'),
            'edit' => EditCalculation::route('/{record}/edit'),
        ];
    }
}
