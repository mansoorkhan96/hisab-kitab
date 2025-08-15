<?php

namespace App\ValueObjects;

use App\Models\Calculation;
use Illuminate\Database\Eloquent\Collection;

use App\Enums\FarmingResourceType;
use App\Helpers\Converter;

use App\Models\Ledger;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Number;

abstract class AbastractCalculationReport
{
    abstract public float $grossRevenue { get; }

    abstract public float $fertilizerExpenseAmount { get; }

    abstract public string $remainingAfterFertilizerExpenseAmount { get; }

    abstract public float $machineAmount { get; }

    abstract public string $remainingAfterMachineAmount { get; }

    abstract public float $implementAndSeedExpenseAmount { get; }

    abstract public string $remainingAfterImplementAndSeedExpenseAmount { get; }

    abstract public float $landlordRevenue { get; }

    abstract public float $farmerGrossRevenue { get; }

    abstract public float $farmerRevenue { get; }

    abstract public float $loanPaymentsAmount { get; }

    abstract public ?Collection $loanPayments { get; }

    abstract public function toArray(): array;

    abstract public static function make(Calculation $calculation): self;

    public static function calculateExpenses(Calculation $calculation): array
    {
        $fertilizerExpenseAmount = Ledger::query()
            ->where('user_id', $calculation->user_id)
            ->where('crop_season_id', $calculation->crop_season_id)
            ->whereHas('farmingResource', fn (Builder $query) => $query->whereIn('type', [FarmingResourceType::Fertilizer, FarmingResourceType::Pesticide]))
            ->sum('amount');

        $implementAndSeedExpenseAmount = Ledger::query()
            ->where('user_id', $calculation->user_id)
            ->where('crop_season_id', $calculation->crop_season_id)
            ->whereHas('farmingResource', fn (Builder $query) => $query->whereIn('type', [FarmingResourceType::Implement, FarmingResourceType::Seed]))
            ->sum('amount');

        return [
            'fertilizerExpenseAmount' => $fertilizerExpenseAmount,
            'implementAndSeedExpenseAmount' => $implementAndSeedExpenseAmount,
        ];
    }

    public static function calculateProfitDistribution(float|int $revenue, array $expenses): array
    {
        // Deduct fertilizer/pesticide costs first
        $afterFertilizerAmount = $revenue - $expenses['fertilizerExpenseAmount'];

        // Machine costs are 1/3 of remaining amount (traditional calculation)
        $machineAmount = round($afterFertilizerAmount / 3);
        $afterMachineAmount = $afterFertilizerAmount - $machineAmount;

        // Final net profit after all expenses
        $netProfit = $afterMachineAmount - $expenses['implementAndSeedExpenseAmount'];

        // Split profit equally between landlord and farmer (traditional 50/50 model)
        $baseProfitShare = $netProfit / 2;

        return [
            'afterFertilizerAmount' => $afterFertilizerAmount,
            'machineAmount' => $machineAmount,
            'afterMachineAmount' => $afterMachineAmount,
            'netProfit' => $netProfit,
            'landlordRevenue' => $baseProfitShare,
            'farmerRevenue' => $baseProfitShare,
        ];
    }

    public static function calculateLoanData(Calculation $calculation): array
    {
        $loanPaymentsAmount = $calculation
            ->loanPayments()
            ->where('user_id', $calculation->user_id)
            ->sum('amount');

        $loanPayments = $calculation
            ->loanPayments()
            ->where('user_id', $calculation->user_id)
            ->get();

        return [
            'loanPaymentsAmount' => $loanPaymentsAmount,
            'loanPayments' => $loanPayments,
        ];
    }
}
