<?php

namespace App\ValueObjects;

use App\Helpers\Converter;
use App\Models\Calculation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Number;

/**
 * Wheat crop calculation report that processes threshing data and calculates
 * profit distribution between landlord and farmer according to Sindhi agricultural practices.
 */
class WheatCropCalculationReport extends AbastractCalculationReport
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
        public float $grossRevenue,
        public float $fertilizerExpenseAmount,
        public string $remainingAfterFertilizerExpenseAmount,
        public float $machineAmount,
        public string $remainingAfterMachineAmount,
        public float $implementAndSeedExpenseAmount,
        public string $remainingAfterImplementAndSeedExpenseAmount,
        public float $landlordRevenue,
        public float $farmerBaseRevenue,
        public ?float $farmerKudhiAmount,
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
        $wheatData = self::calculateWheatData($calculation);
        $amounts = self::calculateAmounts($calculation, $wheatData);
        $expenses = self::calculateExpenses($calculation);
        $profitData = self::calculateProfitDistribution($amounts['grossRevenue'], $expenses);
        $loanData = self::calculateLoanData($calculation);

        // Farmer gets additional benefit from kudhi if any
        $farmerKudhiAmount = 0;

        if ($calculation->kudhi_in_kgs > 0) {
            $farmerKudhiAmount = ($calculation->kudhi_in_kgs / 100) * $calculation->cropSeason->wheat_rate;
        }

        $farmerGrossRevenue = $profitData['farmerRevenue'] + $farmerKudhiAmount;

        return new self(
            totalWheatSacks: Converter::kgsToSacksString($wheatData['totalWeightInKgs']),
            thresher: Converter::kgsToSacksString($wheatData['thresherInKgs']),
            remainingAfterThresher: Converter::kgsToSacksString($wheatData['remainingAfterThresher']),
            kudhi: $calculation->kudhi_in_kgs.' KGs',
            remainingAfterKudhi: Converter::kgsToSacksString($wheatData['remainingAfterKudhi']),
            kamdari: $calculation->kamdari.' KGs',
            remainingAfterKamdari: Converter::kgsToSacksString($wheatData['netWheatWeight']),
            sackAmount: $amounts['sackAmount'],
            buhAmount: $amounts['buhAmount'],
            grossRevenue: $amounts['grossRevenue'],
            fertilizerExpenseAmount: $expenses['fertilizerExpenseAmount'],
            remainingAfterFertilizerExpenseAmount: Number::currency($profitData['afterFertilizerAmount'], 'PKR'),
            machineAmount: $profitData['machineAmount'],
            remainingAfterMachineAmount: Number::currency($profitData['afterMachineAmount'], 'PKR'),
            implementAndSeedExpenseAmount: $expenses['implementAndSeedExpenseAmount'],
            remainingAfterImplementAndSeedExpenseAmount: Number::currency($profitData['netProfit'], 'PKR'),
            landlordRevenue: $profitData['landlordRevenue'],
            farmerBaseRevenue: $profitData['farmerRevenue'],
            farmerKudhiAmount: $farmerKudhiAmount,
            farmerGrossRevenue: $farmerGrossRevenue,
            farmerRevenue: $farmerGrossRevenue - $loanData['loanPaymentsAmount'],
            loanPaymentsAmount: $loanData['loanPaymentsAmount'],
            loanPayments: $loanData['loanPayments'],
        );
    }

    /**
     * Calculate wheat distribution after threshing fees and traditional deductions.
     *
     * Process: Total harvest → Thresher fee (10%) → Kudhi deduction → Kamdari deduction
     */
    private static function calculateWheatData(Calculation $calculation): array
    {
        $totalSacks = $calculation->threshings()->sum('total_wheat_sacks');
        $totalWeightInKgs = $totalSacks * 100; // Convert sacks to kg (1 sack = 100kg)

        // Thresher gets 10% of total wheat as payment
        $thresherInKgs = $totalSacks * 10;
        $remainingAfterThresher = $totalWeightInKgs - $thresherInKgs;

        // Deduct traditional payments: kudhi (village tax) and kamdari (labor share)
        $remainingAfterKudhi = $remainingAfterThresher - $calculation->kudhi_in_kgs;
        $netWheatWeight = $remainingAfterKudhi - $calculation->kamdari;

        return [
            'totalWeightInKgs' => $totalWeightInKgs,
            'totalSacks' => $totalSacks,
            'thresherInKgs' => $thresherInKgs,
            'remainingAfterThresher' => $remainingAfterThresher,
            'remainingAfterKudhi' => $remainingAfterKudhi,
            'netWheatWeight' => $netWheatWeight,
        ];
    }

    /**
     * Calculate revenue from wheat grain and straw (buh).
     */
    private static function calculateAmounts(Calculation $calculation, array $wheatData): array
    {
        // Revenue from wheat grain at market rate per 100kg
        $sackAmount = ($wheatData['netWheatWeight'] / 100) * $calculation->cropSeason->wheat_rate;

        // Revenue from wheat straw: 2.5kg straw per sack of wheat
        $buhAmount = ($wheatData['totalSacks'] * 2.5) * $calculation->wheat_straw_rate;

        $grossRevenue = $sackAmount + $buhAmount;

        return [
            'sackAmount' => $sackAmount,
            'buhAmount' => $buhAmount,
            'grossRevenue' => $grossRevenue,
        ];
    }
}
