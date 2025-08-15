<?php

namespace App\ValueObjects;

use App\Helpers\Converter;
use App\Models\Calculation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;

/**
 * Cotton crop calculation report that processes cotton picking data and calculates
 * profit distribution between landlord and farmer according to Sindhi agricultural practices.
 */
class CottonCropCalculationReport extends AbastractCalculationReport
{
    public function __construct(
        public string $totalCottonKgs,
        public float $grossRevenue,
        public float $labourCost,
        public float $amountAfterLabourCost,
        public float $revenue,
        public float $fertilizerExpenseAmount,
        public string $remainingAfterFertilizerExpenseAmount,
        public float $kamdariAmount,
        public float $machineAmount,
        public string $remainingAfterMachineAmount,
        public float $implementAndSeedExpenseAmount,
        public string $remainingAfterImplementAndSeedExpenseAmount,
        public float $landlordRevenue,
        public float $farmerGrossRevenue,
        public float $farmerRevenue,
        public float $loanPaymentsAmount,
        public ?Collection $loanPayments,
    ) {}

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    public static function make(Calculation $calculation): self
    {
        $cottonData = self::calculateCottonData($calculation);
        $amounts = self::calculateAmounts($calculation, $cottonData);
        $expenses = self::calculateExpenses($calculation);
        $profitData = self::calculateProfitDistribution($amounts['revenue'], $expenses);
        $loanData = self::calculateLoanData($calculation);

        return new self(
            totalCottonKgs: Converter::kgsToMunnString($cottonData['totalCottonKgs']),
            grossRevenue: $amounts['grossRevenue'],
            labourCost: $cottonData['labourCost'],
            amountAfterLabourCost: $amounts['amountAfterLabourCost'],
            revenue: $amounts['revenue'],
            fertilizerExpenseAmount: $expenses['fertilizerExpenseAmount'],
            remainingAfterFertilizerExpenseAmount: Number::currency($profitData['afterFertilizerAmount'], 'PKR'),
            kamdariAmount: $calculation->kamdari,
            machineAmount: $profitData['machineAmount'],
            remainingAfterMachineAmount: Number::currency($profitData['afterMachineAmount'], 'PKR'),
            implementAndSeedExpenseAmount: $expenses['implementAndSeedExpenseAmount'],
            remainingAfterImplementAndSeedExpenseAmount: Number::currency($profitData['netProfit'], 'PKR'),
            landlordRevenue: $profitData['landlordRevenue'],
            farmerGrossRevenue: $profitData['farmerRevenue'],
            farmerRevenue: $profitData['farmerRevenue'] - $loanData['loanPaymentsAmount'],
            loanPaymentsAmount: $loanData['loanPaymentsAmount'],
            loanPayments: $loanData['loanPayments'],
        );
    }

    /**
     * Calculate total cotton weight and labour costs from picking data.
     */
    private static function calculateCottonData(Calculation $calculation): array
    {
        // Sum total cotton weight from all cotton picking rounds and their dailies
        $totalCottonKgs = $calculation
            ->user
            ->cottonPickingRounds()
            ->whereBelongsTo($calculation->cropSeason)
            ->with('cottonPickingDailies')
            ->get()
            ->sum(fn ($round) => $round->cottonPickingDailies->sum('kgs_picked'));

        // Calculate labour cost based on total kgs picked and labour rate
        $labourRate = $calculation->cropSeason->cotton_labour_rate_per_kg ?? 0;
        $labourCost = $totalCottonKgs * $labourRate;

        return [
            'totalCottonKgs' => $totalCottonKgs,
            'labourCost' => $labourCost,
        ];
    }

    /**
     * Calculate revenue from cotton at market rate.
     */
    private static function calculateAmounts(Calculation $calculation, array $cottonData): array
    {
        // Revenue from cotton at market rate per kg
        $cottonRate = $calculation->cropSeason->cotton_rate_per_kg ?? 0;
        $grossRevenue = ($cottonData['totalCottonKgs'] / 40) * $cottonRate;

        $amountAfterLabourCost = $grossRevenue - $cottonData['labourCost'];

        $amountAfterKamdari = $amountAfterLabourCost - $calculation->kamdari;

        return [
            'grossRevenue' => $grossRevenue,
            'amountAfterLabourCost' => $amountAfterLabourCost,
            'revenue' => $amountAfterKamdari,
        ];
    }
}
