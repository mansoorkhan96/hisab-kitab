<?php

namespace App\Filament\Components;

use App\Enums\FarmingResourceType;
use App\Models\Calculation;
use App\Models\Ledger;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
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
        $calculation = [];

        $totalSacks = $this->calculation->total_wheat_sacks;
        $calculation['total_wheat_sacks'] = $totalSacks;

        $totalWeightInKgs = $totalSacks * 100;

        // Thresher
        $thresherInKgs = $totalSacks * 10;
        $remainingWeightInKgs = $totalWeightInKgs - $thresherInKgs;
        $calculation['thresher'] = $this->kgsToSacksString($thresherInKgs);
        $calculation['remaining_after_thresher'] = $this->kgsToSacksString($remainingWeightInKgs);

        // Kudhi
        $remainingWeightInKgs -= ($this->calculation->kudhi * 100);
        $calculation['kudhi'] = $this->calculation->kudhi.' Borion';
        $calculation['remaining_after_kudhi'] = $this->kgsToSacksString($remainingWeightInKgs);

        // TODO: make it flexible
        // Kamdari
        // $kamdari = $totalSacks * 5;
        // $remainingWeightInKgs = $remainingWeightInKgs - $kamdari; // Kamdari
        // $calculation['kamdari'] = $this->kgsToSacksString($kamdari);
        // $calculation['remaining_after_kamdari'] = $this->kgsToSacksString($remainingWeightInKgs);

        // Total amount
        $sackAmount = ($remainingWeightInKgs / 100) * $this->calculation->wheat_rate;
        $calculation['sack_amount'] = $sackAmount;

        // Buh amount
        $buhAmount = ($totalSacks * 2.5) * $this->calculation->wheat_straw_rate;
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
        $calculation['remaining_after_fertilizer_expense_amount'] = $amount;

        // Machine Amount
        $machineAmount = ($amount / 3);
        $amount -= $machineAmount;
        $calculation['machine_amount'] = $machineAmount;
        $calculation['remaining_after_machine_amount'] = $amount;

        // Harr & Bij
        $implementAndSeedExpenseAmount = Ledger::query()
            ->where('farmer_id', $this->calculation->farmer_id)
            ->where('crop_season_id', $this->calculation->crop_season_id)
            ->whereHas('farmingResource', fn (Builder $query) => $query->whereIn('type', [FarmingResourceType::Implement, FarmingResourceType::Seed]))
            ->sum('amount');

        $amount -= $implementAndSeedExpenseAmount;
        $calculation['implement_and_seed_expense_amount'] = $implementAndSeedExpenseAmount;
        $calculation['remaining_after_implement_and_seed_expense_amount'] = $amount;

        // Farmer/Landlord profit
        $dividedProfit = $amount / 2;
        $calculation['farmer_amount'] = $dividedProfit;
        $calculation['landlord_amount'] = $dividedProfit;

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
                // TextEntry::make('kamdari')
                //     ->prefix('-')
                //     ->color('danger')
                //     ->helperText('Baqi: '.$calculation['remaining_after_kamdari'])
                //     ->inlineLabel(),
                TextEntry::make('sack_amount')
                    ->label('Total Ann Amount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('buh_amount')
                    ->color('success')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('amount')
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
                TextEntry::make('farmer_amount')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
                TextEntry::make('landlord_amount')
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->money('PKR')
                    ->inlineLabel(),
            ]);
    }

    protected function kgsToSacksString(int|float $kgs): string
    {
        return str(floor($kgs / 100))
            ->append(' Borion')
            ->when(
                ($remainingKgs = fmod($kgs, 100)) > 0,
                fn ($str) => $str
                    ->append(', ')
                    ->append($remainingKgs)
                    ->append(' KGs')
            );
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
