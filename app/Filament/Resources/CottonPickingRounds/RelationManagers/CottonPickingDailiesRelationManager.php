<?php

namespace App\Filament\Resources\CottonPickingRounds\RelationManagers;

use App\Helpers\Converter;
use App\Models\CottonPickingDaily;
use App\Models\Labourer;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class CottonPickingDailiesRelationManager extends RelationManager
{
    protected static string $relationship = 'cottonPickingDailies';

    protected $listeners = [
        '$refresh' => '$refresh',
    ];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                DatePicker::make('picking_date')
                    ->native(false)
                    ->hidden(fn ($operation) => str_starts_with($operation, 'edit'))
                    ->rule(function ($operation) {
                        if (str_starts_with($operation, 'edit')) {
                            return;
                        }

                        return function (string $attribute, $value, \Closure $fail) {
                            $exists = CottonPickingDaily::query()
                                ->whereBelongsTo($this->getOwnerRecord())
                                ->where('picking_date', Carbon::parse($value)->toDateString())
                                ->exists();

                            if ($exists) {
                                $fail('Cotton picking records already exist for this date.');
                            }
                        };
                    })
                    ->required(),
                Repeater::make('cotton_picking_daily')
                    ->label('Cotton Picking Daily')
                    ->table([
                        TableColumn::make('Name')
                            ->alignLeft(),
                        TableColumn::make('Kgs Picked (e.g. 2+3)')
                            ->alignLeft(),
                        TableColumn::make('Kgs Picked (Total)')
                            ->alignLeft(),
                    ])
                    ->schema([
                        Hidden::make('labourer_id'),
                        TextInput::make('name')
                            ->disabled()
                            ->columnSpanFull(),
                        TextInput::make('kgs_picked_raw')
                            ->lazy()
                            ->dehydrated(false)
                            ->default(0)
                            ->rule(function () {
                                return function (string $attribute, $value, \Closure $fail) {
                                    if (! preg_match('/^[0-9]+(\+[0-9]+)*$/', $value)) {
                                        $fail('Only numbers separated by + are allowed (e.g. 2+3+4)');
                                    }
                                };
                            })
                            ->afterStateUpdated(function ($state, Set $set, self $livewire, TextInput $component) {
                                $livewire->validateOnly($component->getStatePath());

                                $state .= ';';

                                $set('kgs_picked', eval("return $state"));
                            })
                            ->minValue(0),
                        TextInput::make('kgs_picked')
                            ->default(0)
                            ->label('Kgs Picked (Total)')
                            ->readOnly(),
                    ])
                    ->addable(false)
                    ->deletable(false)
                    ->reorderable(false),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('name', 'asc')
            ->query(fn () => Labourer::query()
                ->withWhereHas(
                    'cottonPickingDailies',
                    fn ($query) => $query->whereBelongsTo($this->getOwnerRecord()),
                ))
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                ...$this->getCottonPickingDailyColumns(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                $this->getCreateAction(),
            ]);
    }

    public function getCottonPickingDailyColumns(): array
    {
        return $this
            ->getOwnerRecord()
            ->cottonPickingDailies()
            ->distinct()
            ->pluck('picking_date')
            ->map(function (Carbon $date, int $index) {
                return TextColumn::make($date)
                    ->label($date->format('d-m'))
                    ->getStateUsing(function (Labourer $labourer) use ($date) {
                        $kgsPicked = $labourer
                            ->cottonPickingDailies()
                            ->where('picking_date', $date)
                            ->value('kgs_picked');

                        return Converter::kgsToMunnString($kgsPicked);
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label('Total')
                            ->using(function (Builder $query) use ($date) {
                                $kgsPicked = CottonPickingDaily::query()
                                    ->whereBelongsTo($this->getOwnerRecord())
                                    ->where('picking_date', $date->toDateString())
                                    ->whereIn('labourer_id', $query->pluck('id'))
                                    ->sum('kgs_picked');

                                return Converter::kgsToMunnString($kgsPicked);
                            })
                    )
                    ->action($this->getEditAction($date, $index));
            })
            ->concat([
                TextColumn::make('total')
                    ->label('Total')
                    ->weight(FontWeight::Bold)
                    ->getStateUsing(function (Labourer $labourer) {
                        $kgsPicked = $labourer->cottonPickingDailies->sum('kgs_picked');

                        return Converter::kgsToMunnString($kgsPicked);
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label('Grand Total')
                            ->using(function (Builder $query) {
                                $kgsPicked = CottonPickingDaily::query()
                                    ->whereBelongsTo($this->getOwnerRecord())
                                    ->sum('kgs_picked');

                                return Converter::kgsToMunnString($kgsPicked);
                            })
                    ),
            ])
            ->concat([
                TextColumn::make('payment')
                    ->money('PKR')
                    ->getStateUsing(function (Labourer $labourer) {
                        $totalKgs = $labourer->cottonPickingDailies->sum('kgs_picked');
                        $ratePerKg = $this->getOwnerRecord()->cropSeason->cotton_labour_rate_per_kg ?? 0;

                        return $totalKgs * $ratePerKg;
                    })
                    ->summarize(
                        Summarizer::make()
                            ->label('Total Payment')
                            ->money('PKR')
                            ->using(function (Builder $query) {
                                $totalKgs = CottonPickingDaily::query()
                                    ->whereBelongsTo($this->getOwnerRecord())
                                    ->whereIn('labourer_id', $query->pluck('id'))
                                    ->sum('kgs_picked');

                                $ratePerKg = $this->getOwnerRecord()->cropSeason->cotton_labour_rate_per_kg ?? 0;

                                return $totalKgs * $ratePerKg;
                            })
                    ),
            ])
            ->toArray();
    }

    public function getCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->label('Create Daily')
            ->modalHeading('Create Daily')
            ->fillForm([
                'picking_date' => now(),
                'cotton_picking_daily' => Labourer::query()
                    ->orderBy('name')
                    ->get()
                    ->map(fn ($labourer) => [
                        'labourer_id' => $labourer->getKey(),
                        'name' => $labourer->name,
                        'kgs_picked' => 0,
                    ]),
            ])
            ->before($this->beforeSavingForm(...))
            ->action(function (array $data) {
                $cottonPickingDaily = array_map(fn ($labourer) => [
                    'labourer_id' => $labourer['labourer_id'],
                    'picking_date' => $data['picking_date'],
                    'kgs_picked' => $labourer['kgs_picked'] ?? 0,
                ], $data['cotton_picking_daily']);

                $this
                    ->getOwnerRecord()
                    ->cottonPickingDailies()
                    ->createMany($cottonPickingDaily);

                $this->dispatch('$refresh');
            });
    }

    public function getEditAction(Carbon $date, int $index): EditAction
    {
        return EditAction::make('edit'.$date->toDateString().$index)
            ->modalHeading('Edit Daily for '.$date->format('d-m'))
            ->fillForm(fn () => [
                'cotton_picking_daily' => Labourer::query()
                    ->with([
                        'cottonPickingDailies' => fn ($query) => $query
                            ->whereBelongsTo($this->getOwnerRecord())
                            ->where('picking_date', $date->toDateString()),
                    ])
                    ->orderBy('name')
                    ->get()
                    ->map(fn ($labourer) => [
                        'labourer_id' => $labourer->getKey(),
                        'name' => $labourer->name,
                        'kgs_picked_raw' => $labourer->cottonPickingDailies->value('kgs_picked'),
                        'kgs_picked' => $labourer->cottonPickingDailies->value('kgs_picked'),
                    ]),
            ])
            ->before($this->beforeSavingForm(...))
            ->action(function (array $data) use ($date) {
                foreach ($data['cotton_picking_daily'] as $labourer) {
                    $this
                        ->getOwnerRecord()
                        ->cottonPickingDailies()
                        ->where('labourer_id', $labourer['labourer_id'])
                        ->where('picking_date', $date->toDateString())
                        ->update([
                            'kgs_picked' => $labourer['kgs_picked'] ?? 0,
                        ]);
                }

                $this->dispatch('$refresh');
            });
    }

    public function beforeSavingForm(EditAction|CreateAction $action, array $data)
    {
        if (array_sum(Arr::pluck($data['cotton_picking_daily'], 'kgs_picked')) <= 0) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body('Please enter at least one labourer with a non-zero kgs picked.')
                ->send();

            $action->halt();
        }
    }
}
