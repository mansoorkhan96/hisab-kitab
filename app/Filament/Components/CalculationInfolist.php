<?php

namespace App\Filament\Components;

use App\Helpers\Converter;
use App\Models\Calculation;
use App\ValueObjects\CalculationResult;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class CalculationInfolist extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    #[Reactive]
    public Calculation $calculation;

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
                    ->label('Machine Amount')
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
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerAmount')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerKudhiAmount')
                    ->visible($this->calculation->kudhi_in_kgs && $this->calculation->kudhi_in_kgs > 0)
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerFinalAmount')
                    ->visible($this->calculation->kudhi_in_kgs && $this->calculation->kudhi_in_kgs > 0)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerLoan')
                    ->color('warning')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmerProfitLost')
                    ->label(fn ($state) => $state > 0 ? 'Farmer Profit' : 'Farmer Debt')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
            ]);
    }

    protected function kgsToSacksString(int|float $kgs): string
    {
        return Converter::kgsToSacksString($kgs);
    }

    public function render()
    {
        return <<<'HTML'
            <div>
                {{ $this->infolist }}
            </div>
        HTML;
    }
}
