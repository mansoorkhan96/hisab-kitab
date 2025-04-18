<?php

namespace App\Filament\Components;

use App\Enums\FarmingResourceType;
use App\Helpers\Converter;
use App\Models\Calculation;
use App\Models\FarmerLoan;
use App\Models\Ledger;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
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
        // TODO: refactor to a class/value object
        $calculation = [];

        // $sacks = explode('/', $this->calculation->total_wheat_sacks);
        $sacks = $this->calculation->threshings()->sum('total_wheat_sacks');

        // $totalWeightInKgs = ($sacks[0] * 100) + Arr::get($sacks, 1, 0);
        $totalWeightInKgs = $sacks * 100;
        $totalSacks = $totalWeightInKgs / 100;

        $calculation['total_wheat_sacks'] = $this->kgsToSacksString($totalWeightInKgs);

        $thresherInKgs = $totalSacks * 10;

        $remainingWeightInKgs = $totalWeightInKgs - $thresherInKgs;
        $calculation['thresher'] = $this->kgsToSacksString($thresherInKgs);
        $calculation['remaining_after_thresher'] = $this->kgsToSacksString($remainingWeightInKgs);

        // Kudhi
        $remainingWeightInKgs -= ($this->calculation->kudhi_in_kgs);
        $calculation['kudhi'] = $this->calculation->kudhi_in_kgs.' KGs';
        $calculation['remaining_after_kudhi'] = $this->kgsToSacksString($remainingWeightInKgs);

        // Kamdari
        $remainingWeightInKgs -= ($this->calculation->kamdari_in_kgs);
        $calculation['kamdari'] = $this->calculation->kamdari_in_kgs.' KGs';
        $calculation['remaining_after_kamdari'] = $this->kgsToSacksString($remainingWeightInKgs);

        // Total amount
        $sackAmount = ($remainingWeightInKgs / 100) * $this->calculation->wheat_rate;
        $calculation['sack_amount'] = $sackAmount;

        // Buh amount
        $buhAmount = ($totalSacks * 2.5) * $this->calculation->wheat_straw_rate;
        // TODO: temporary fix to not include remaining kgs
        $buhAmount = ($sacks * 2.5) * $this->calculation->wheat_straw_rate;
        $calculation['buh_amount'] = $buhAmount;

        // Total amount
        $amount = $sackAmount + $buhAmount;
        $calculation['amount'] = $amount;

        // Dawa & color
        $fertilizerExpenseAmount = Ledger::query()
            ->where('farmer_id', $this->calculation->farmer_id)
            ->where('crop_season_id', $this->calculation->crop_season_id)
            ->whereHas('farmingResource', fn (Builder $query) => $query->whereIn('type', [FarmingResourceType::Fertilizer, FarmingResourceType::Pesticide]))
            ->sum('amount');

        $amount -= $fertilizerExpenseAmount;
        $calculation['fertilizer_expense_amount'] = $fertilizerExpenseAmount;
        $calculation['remaining_after_fertilizer_expense_amount'] = Number::currency($amount, 'PKR');

        // Machine Amount
        $machineAmount = ($amount / 3);
        $amount -= $machineAmount;
        $calculation['machine_amount'] = $machineAmount;
        $calculation['remaining_after_machine_amount'] = Number::currency($amount, 'PKR');

        // Harr & Bij
        $implementAndSeedExpenseAmount = Ledger::query()
            ->where('farmer_id', $this->calculation->farmer_id)
            ->where('crop_season_id', $this->calculation->crop_season_id)
            ->whereHas('farmingResource', fn (Builder $query) => $query->whereIn('type', [FarmingResourceType::Implement, FarmingResourceType::Seed]))
            ->sum('amount');

        $amount -= $implementAndSeedExpenseAmount;
        $calculation['implement_and_seed_expense_amount'] = $implementAndSeedExpenseAmount;
        $calculation['remaining_after_implement_and_seed_expense_amount'] = Number::currency($amount, 'PKR');

        // Farmer/Landlord profit
        $dividedProfit = $amount / 2;
        $farmerAmount = $dividedProfit;
        $calculation['landlord_amount'] = $dividedProfit;
        $calculation['farmer_amount'] = $farmerAmount;

        if ($this->calculation->kudhi_in_kgs && $this->calculation->kudhi_in_kgs > 0) {
            $calculation['farmer_kudhi_amount'] = ($this->calculation->kudhi_in_kgs / 100) * $this->calculation->wheat_rate;
            $farmerAmount = $calculation['farmer_final_amount'] = $calculation['farmer_amount'] + $calculation['farmer_kudhi_amount'];
        }

        // Substract Farmer loan
        $farmerLoan = FarmerLoan::query()
            ->whereNull('paid_at')
            ->where('farmer_id', $this->calculation->farmer_id)
            ->sum('amount');
        $farmerAmount -= $farmerLoan;
        $calculation['farmer_loan'] = $farmerLoan;
        $calculation['farmer_profit_lost'] = $farmerAmount;

        return $infolist
            ->state($calculation)
            ->schema([
                TextEntry::make('total_wheat_sacks')
                    ->label('Batai')
                    ->inlineLabel(),
                TextEntry::make('thresher')
                    ->prefix('-')
                    ->color('danger')
                    ->helperText('Baqi: '.$calculation['remaining_after_thresher'])
                    ->inlineLabel(),
                TextEntry::make('kudhi')
                    ->prefix('-')
                    ->color('danger')
                    ->helperText('Baqi: '.$calculation['remaining_after_kudhi'])
                    ->inlineLabel(),
                TextEntry::make('kamdari')
                    ->visible($this->calculation->kamdari_in_kgs && $this->calculation->kamdari_in_kgs > 0)
                    ->prefix('-')
                    ->color('danger')
                    ->helperText('Baqi: '.$calculation['remaining_after_kamdari'])
                    ->inlineLabel(),
                TextEntry::make('sack_amount')
                    ->label('Ann Amount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('buh_amount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('amount')
                    ->label('Total Amount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('fertilizer_expense_amount')
                    ->label('Dawa & Color')
                    ->prefix('-')
                    ->color('danger')
                    ->money('PKR')
                    ->helperText('Remaning: '.$calculation['remaining_after_fertilizer_expense_amount'])
                    ->inlineLabel(),
                TextEntry::make('machine_amount')
                    ->label('Machine Amount')
                    ->color('info')
                    ->money('PKR')
                    ->helperText('Remaning: '.$calculation['remaining_after_machine_amount'])
                    ->inlineLabel(),
                TextEntry::make('implement_and_seed_expense_amount')
                    ->label('Harr & Bijj')
                    ->prefix('-')
                    ->color('danger')
                    ->money('PKR')
                    ->helperText('Remaning: '.$calculation['remaining_after_implement_and_seed_expense_amount'])
                    ->inlineLabel(),
                TextEntry::make('landlord_amount')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmer_amount')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmer_kudhi_amount')
                    ->visible($this->calculation->kudhi_in_kgs && $this->calculation->kudhi_in_kgs > 0)
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmer_final_amount')
                    ->visible($this->calculation->kudhi_in_kgs && $this->calculation->kudhi_in_kgs > 0)
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmer_loan')
                    ->color('warning')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('farmer_profit_lost')
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
