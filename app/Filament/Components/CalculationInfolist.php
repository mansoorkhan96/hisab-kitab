<?php

namespace App\Filament\Components;

use App\Models\Calculation;
use App\Models\LoanPayment;
use App\ValueObjects\CalculationResult;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Livewire\Component;

class CalculationInfolist extends Component implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithInfolists;

    protected $listeners = [
        '$refresh' => '$refresh',
    ];

    public Calculation $calculation;

    public bool $printMode = false;

    public function infolist(Schema $schema): Schema
    {
        $calculation = CalculationResult::make($this->calculation);

        return $schema
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
                            ->label(fn (LoanPayment $record) => $record->notes)
                            ->color('danger')
                            ->prefix('-')
                            ->money('PKR')
                            ->hintActions([
                                Action::make('delete')
                                    ->requiresConfirmation()
                                    ->iconButton()
                                    ->icon(Heroicon::OutlinedTrash)
                                    ->color('danger')
                                    ->action(function (LoanPayment $record) {
                                        $record->delete();

                                        $this->dispatch('$refresh');
                                    }),
                            ]),
                    ])
                    ->aboveContent('Subtract Farmer Loan from calculation profit.')
                    ->hintActions([
                        Action::make('subtract_loan')
                            ->disabled(fn () => $calculation->farmerProfitLoss <= 0)
                            ->tooltip(fn () => $calculation->farmerProfitLoss <= 0 ? 'Can\'t substract loan from this calculation, farmer is already in loss. ' : '')
                            ->icon(fn () => Heroicon::Minus)
                            ->button()
                            ->color(Color::Red)
                            ->label('Subtract Loan')
                            ->modalWidth(Width::ScreenSmall)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('Amount')
                                    ->columnSpanFull()
                                    ->required()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(fn () => $calculation->farmerProfitLoss > 0 ? $calculation->farmerProfitLoss : 0),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull(),
                            ])
                            ->action(function (array $data, self $livewire) {
                                $data['user_id'] = $this->calculation->user_id;

                                $this->calculation->loanPayments()->create($data);

                                $livewire->dispatch('$refresh');
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
