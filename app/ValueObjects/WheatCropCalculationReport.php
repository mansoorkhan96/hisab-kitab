<?php

namespace App\ValueObjects;

use App\Enums\FarmingResourceType;
use App\Helpers\Converter;
use App\Models\Calculation;
use App\Models\Ledger;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;

readonly class WheatCropCalculationReport
{
    public function __construct(
        public string $totalWheatSacks,
        public string $thresher,
        public string $remainingAfterThresher,
        public string $kudhi,
        public string $remainingAfterKudhi,
        public string $kamdari,
        public string $remainingAfterKamdari,
        public float $sackAmount,
        public float $buhAmount,
        public float $amount,
        public float $fertilizerExpenseAmount,
        public string $remainingAfterFertilizerExpenseAmount,
        public float $machineAmount,
        public string $remainingAfterMachineAmount,
        public float $implementAndSeedExpenseAmount,
        public string $remainingAfterImplementAndSeedExpenseAmount,
        public float $landlordAmount,
        public float $farmerAmount,
        public ?float $farmerKudhiAmount,
        public ?float $farmerFinalAmount,
        public float $farmerLoan,
        public float $farmerProfitLoss,
        public float $loanPaymentsAmount,
        public ?Collection $loanPayments,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function make(Calculation $calculation): self
    {
        $cropSeason = $calculation->cropSeason;

        $sacks = $calculation->threshings()->sum('total_wheat_sacks');
        $totalWeightInKgs = $sacks * 100;
        $totalSacks = $totalWeightInKgs / 100;

        $thresherInKgs = $totalSacks * 10;
        $remainingWeightInKgs = $totalWeightInKgs - $thresherInKgs;

        // Kudhi
        $remainingWeightInKgs -= ($calculation->kudhi_in_kgs);

        // Kamdari
        $remainingWeightInKgs -= ($calculation->kamdari_in_kgs);

        // Total amount
        $sackAmount = ($remainingWeightInKgs / 100) * $cropSeason->wheat_rate;

        // Buh amount
        $buhAmount = ($sacks * 2.5) * $calculation->wheat_straw_rate;

        // Total amount
        $amount = $sackAmount + $buhAmount;

        // Dawa & color
        $fertilizerExpenseAmount = Ledger::query()
            ->where('user_id', $calculation->user_id)
            ->where('crop_season_id', $calculation->crop_season_id)
            ->whereHas('farmingResource', fn (Builder $query) => $query->whereIn('type', [FarmingResourceType::Fertilizer, FarmingResourceType::Pesticide]))
            ->sum('amount');

        $amount -= $fertilizerExpenseAmount;

        // Machine Amount
        $machineAmount = round($amount / 3);
        $amount -= $machineAmount;

        // Harr & Bij
        $implementAndSeedExpenseAmount = Ledger::query()
            ->where('user_id', $calculation->user_id)
            ->where('crop_season_id', $calculation->crop_season_id)
            ->whereHas('farmingResource', fn (Builder $query) => $query->whereIn('type', [FarmingResourceType::Implement, FarmingResourceType::Seed]))
            ->sum('amount');

        $amount -= $implementAndSeedExpenseAmount;

        // Farmer/Landlord profit
        $dividedProfit = $amount / 2;
        $farmerAmount = $dividedProfit;

        $farmerKudhiAmount = null;
        $farmerFinalAmount = null;

        if ($calculation->kudhi_in_kgs && $calculation->kudhi_in_kgs > 0) {
            $farmerKudhiAmount = ($calculation->kudhi_in_kgs / 100) * $cropSeason->wheat_rate;
            $farmerAmount = $farmerFinalAmount = $farmerAmount + $farmerKudhiAmount;
        }

        $farmerLoan = Loan::query()
            ->where('user_id', $calculation->user_id)
            ->sum('amount');

        $loanPaymentsAmount = $calculation
            ->loanPayments()
            ->where('user_id', $calculation->user_id)
            ->sum('amount');

        $farmerLoan -= $loanPaymentsAmount;

        // $farmerAmount -= $farmerLoan;

        $loanPayments = $calculation
            ->loanPayments()
            ->where('user_id', $calculation->user_id)
            ->get();

        $farmerAmount -= $loanPaymentsAmount;

        return new self(
            totalWheatSacks: Converter::kgsToSacksString($totalWeightInKgs),
            thresher: Converter::kgsToSacksString($thresherInKgs),
            remainingAfterThresher: Converter::kgsToSacksString($remainingWeightInKgs + $calculation->kudhi_in_kgs + $calculation->kamdari_in_kgs),
            kudhi: $calculation->kudhi_in_kgs.' KGs',
            remainingAfterKudhi: Converter::kgsToSacksString($remainingWeightInKgs + $calculation->kamdari_in_kgs),
            kamdari: $calculation->kamdari_in_kgs.' KGs',
            remainingAfterKamdari: Converter::kgsToSacksString($remainingWeightInKgs),
            sackAmount: $sackAmount,
            buhAmount: $buhAmount,
            amount: $amount + $fertilizerExpenseAmount + $machineAmount + $implementAndSeedExpenseAmount,
            fertilizerExpenseAmount: $fertilizerExpenseAmount,
            remainingAfterFertilizerExpenseAmount: Number::currency($amount + $machineAmount + $implementAndSeedExpenseAmount, 'PKR'),
            machineAmount: $machineAmount,
            remainingAfterMachineAmount: Number::currency($amount + $implementAndSeedExpenseAmount, 'PKR'),
            implementAndSeedExpenseAmount: $implementAndSeedExpenseAmount,
            remainingAfterImplementAndSeedExpenseAmount: Number::currency($amount, 'PKR'),
            landlordAmount: $dividedProfit,
            farmerAmount: $dividedProfit,
            farmerKudhiAmount: $farmerKudhiAmount,
            farmerFinalAmount: $farmerFinalAmount,
            farmerLoan: $farmerLoan,
            farmerProfitLoss: $farmerAmount,
            loanPaymentsAmount: $loanPaymentsAmount,
            loanPayments: $loanPayments,
        );
    }
}
