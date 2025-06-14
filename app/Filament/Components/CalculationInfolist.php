<?php

namespace App\Filament\Components;

use App\Models\Calculation;
use App\ValueObjects\CalculationResult;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\MaxWidth;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CalculationInfolist extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    #[Reactive]
    public Calculation $calculation;

    public bool $printMode = false;

    public function infolist(Infolist $infolist): Infolist
    {
        $calculation = CalculationResult::make($this->calculation);

        return $infolist
            ->state($calculation->toArray())
            ->schema([
                TextEntry::make('totalWheatSacks')
                    ->label('Batai')
                    ->inlineLabel(),
                TextEntry::make('thresher')
                    ->prefix('-')
                    ->color('danger')
                    ->helperText('Baqi: '.$calculation->remainingAfterThresher)
                    ->inlineLabel(),
                TextEntry::make('kudhi')
                    ->prefix('-')
                    ->color('danger')
                    ->helperText('Baqi: '.$calculation->remainingAfterKudhi)
                    ->inlineLabel(),
                TextEntry::make('kamdari')
                    ->visible($this->calculation->kamdari_in_kgs && $this->calculation->kamdari_in_kgs > 0)
                    ->prefix('-')
                    ->color('danger')
                    ->helperText('Baqi: '.$calculation->remainingAfterKamdari)
                    ->inlineLabel(),
                TextEntry::make('sackAmount')
                    ->label('Ann Amount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('buhAmount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('amount')
                    ->label('Total Amount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('fertilizerExpenseAmount')
                    ->label('Dawa & Color')
                    ->prefix('-')
                    ->color('danger')
                    ->money('PKR')
                    ->helperText('Remaning: '.$calculation->remainingAfterFertilizerExpenseAmount)
                    ->inlineLabel(),
                TextEntry::make('machineAmount')
                    ->label('Machine Amt')
                    ->color('info')
                    ->money('PKR')
                    ->helperText('Remaning: '.$calculation->remainingAfterMachineAmount)
                    ->inlineLabel(),
                TextEntry::make('implementAndSeedExpenseAmount')
                    ->label('Harr & Bijj')
                    ->prefix('-')
                    ->color('danger')
                    ->money('PKR')
                    ->helperText('Remaning: '.$calculation->remainingAfterImplementAndSeedExpenseAmount)
                    ->inlineLabel(),
                TextEntry::make('landlordAmount')
                    ->label('Landlord Amt')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerAmount')
                    ->label('Farmer Amt')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerKudhiAmount')
                    ->label('Kudhi Amount')
                    ->visible($this->calculation->kudhi_in_kgs && $this->calculation->kudhi_in_kgs > 0)
                    ->prefix('+')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerFinalAmount')
                    ->label('Farmer Total')
                    ->visible($this->calculation->kudhi_in_kgs && $this->calculation->kudhi_in_kgs > 0)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                RepeatableEntry::make('loanPayments')
                    ->label('Loan Payments')
                    ->key('loan_payment')
                    ->hidden(fn () => $this->printMode)
                    ->schema([
                        TextEntry::make('amount')
                            ->hiddenLabel()
                            ->color('danger')
                            ->helperText(fn ($record) => $record->notes)
                            ->prefix('-')
                            ->money('PKR'),
                    ])
                    ->helperText('Add loan payments that are deducted from current calculation.')
                    ->hintActions([
                        Action::make('add_loan_payment')
                            ->icon('heroicon-m-plus')
                            ->button()
                            ->label('Add Loan Payment')
                            ->modalWidth(MaxWidth::ScreenSmall)
                            ->form([
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->columnSpanFull()
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull(),
                            ])
                            ->action(function (array $data) {
                                $data['farmer_id'] = $this->calculation->farmer_id;

                                $this->calculation->loanPayments()->create($data);
                            }),
                    ]),
                TextEntry::make('loanPaymentsAmount')
                    ->label('Loan Payments')
                    ->prefix('-')
                    ->visible(fn () => $this->printMode)
                    ->color('danger')
                    ->money('PKR')
                    ->inlineLabel(),
                // TextEntry::make('farmerLoan')
                //     ->color('warning')
                //     ->money('PKR')
                //     ->inlineLabel(),
                TextEntry::make('farmerProfitLoss')
                    ->label('Farmer Total')
                    // ->label(fn ($state) => $state > 0 ? 'Farmer Profit' : 'Farmer Debt')
                    // ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
            ]);
    }

    public function render()
    {
        return view('filament.components.calculation-infolist');
    }
}
