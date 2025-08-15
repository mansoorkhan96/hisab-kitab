<?php

namespace App\Contracts;

use App\Models\Calculation;
use Illuminate\Database\Eloquent\Collection;

interface CalculationReport
{
    public float $grossRevenue { get; }

    public float $fertilizerExpenseAmount { get; }

    public string $remainingAfterFertilizerExpenseAmount { get; }

    public float $machineAmount { get; }

    public string $remainingAfterMachineAmount { get; }

    public float $implementAndSeedExpenseAmount { get; }

    public string $remainingAfterImplementAndSeedExpenseAmount { get; }

    public float $landlordRevenue { get; }

    public float $farmerBaseRevenue { get; }

    public float $farmerGrossRevenue { get; }

    public float $farmerRevenue { get; }

    public float $loanPaymentsAmount { get; }

    public ?Collection $loanPayments { get; }

    public function toArray(): array;

    public static function make(Calculation $calculation): self;
}
