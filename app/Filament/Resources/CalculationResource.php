<?php

namespace App\Filament\Resources;

use App\Enums\FarmingResourceType;
use App\Filament\Components\CalculationInfolist;
use App\Filament\Resources\CalculationResource\Pages;
use App\Filament\Resources\CalculationResource\Pages\EditCalculation;
use App\Filament\Widgets\FarmerLoansTableWidget;
use App\Filament\Widgets\LedgersTableWidget;
use App\Models\Calculation;
use Filament\Forms\Components\Actions\Action;
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
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class CalculationResource extends Resource
{
    protected static ?string $model = Calculation::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('crop_season_id')
                    ->live()
                    ->relationship('cropSeason', 'name')
                    ->preload()
                    ->required(),
                Select::make('farmer_id')
                    ->live()
                    ->relationship('farmer', 'name')
                    ->preload()
                    ->required(),
                TextInput::make('wheat_rate')
                    ->live()
                    ->prefixIcon('heroicon-m-currency-dollar')
                    ->numeric(),
                TextInput::make('total_wheat_sacks')
                    ->live(),
                TextInput::make('kudhi')
                    ->live()
                    ->numeric(),
                TextInput::make('wheat_straw_rate')
                    ->live()
                    ->prefixIcon('heroicon-m-currency-dollar')
                    ->numeric(),
                TextInput::make('thresher')
                    ->dehydrated(false)
                    ->live(),
                Toggle::make('hide_details')
                    ->live()
                    ->dehydrated(false),
                Placeholder::make('toggle_finalized_at_placeholder')
                    ->label(
                        fn (Calculation $calculation) => filled($calculation->finalized_at)
                            ? 'Calculation was finalized on: '.$calculation->finalized_at->format('F j, Y')
                            : ''
                    )
                    ->helperText('Finalizing the calculation declares that the future farmer loans should not be added to this calculation.')
                    ->key('toggle_finalized_at_placeholder_key')
                    ->hintAction(
                        Action::make('toggle_finalized_at')
                            ->button()
                            ->requiresConfirmation(fn (Calculation $calculation) => filled($calculation->finalized_at))
                            ->modalDescription(
                                fn (Calculation $calculation) => filled($calculation->finalized_at)
                                    ? new HtmlString('After reopening this calculation, all of the '.$calculation->farmer->name."'s loans lended after ".$calculation->finalized_at->format('F j, Y').' will be added to this calculation!<br><br> Are you sure you want reopen this calculation?')
                                    : '')
                            ->label(
                                fn (Calculation $calculation) => empty($calculation->finalized_at)
                                    ? 'Finalize Calculation'
                                    : 'Re-Open Calculation'
                            )
                            ->color(
                                fn (Calculation $calculation) => empty($calculation->finalized_at)
                                    ? 'success'
                                    : 'warning'
                            )
                            ->icon(
                                fn (Calculation $calculation) => empty($calculation->finalized_at)
                                    ? 'heroicon-m-check-badge'
                                    : 'heroicon-m-book-open'
                            )
                            ->visible(fn ($context) => $context !== 'create')
                            ->action(function (Calculation $calculation) {
                                if (empty($calculation->finalized_at)) {
                                    $calculation->update(['finalized_at' => now()]);
                                } else {
                                    $calculation->update(['finalized_at' => null]);
                                }
                            })
                    ),
                Split::make([
                    Section::make('Calculation')
                        ->schema([
                            Placeholder::make('alert')
                                ->disabled()
                                ->label('Please save the form to see the calucation...')
                                ->visible(fn (string $context) => $context === 'create'),
                            Livewire::make(
                                CalculationInfolist::class,
                                fn (EditCalculation $livewire, Get $get) => [
                                    'calculation' => $livewire->getRecord(),
                                    'thresher' => $get('thresher'),
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
                            'groupsOnly' => $get('hide_details'),
                        ])->key('dawa-color'),
                        Livewire::make(LedgersTableWidget::class, fn (Get $get) => [
                            'farmer_id' => $get('farmer_id'),
                            'crop_season_id' => $get('crop_season_id'),
                            'farmingResourceTypes' => [FarmingResourceType::Implement, FarmingResourceType::Seed],
                            'tableHeading' => 'Harr & Bij',
                            'groupsOnly' => $get('hide_details'),
                        ])->key('harr-bij'),
                        Livewire::make(
                            FarmerLoansTableWidget::class,
                            fn (Get $get) => ['farmer_id' => $get('farmer_id')]
                        )->key('farmer-loan'),
                    ]),
                ])
                    ->from('md')
                    ->columnSpanFull(),
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
                //
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
