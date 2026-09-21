<?php

use App\ValueObjects\AbastractCalculationReport;

it('calculates profit distribution with traditional one-third machine share and fifty-fifty split', function () {
    $result = AbastractCalculationReport::calculateProfitDistribution(1000, [
        'fertilizerExpenseAmount' => 100,
        'implementAndSeedExpenseAmount' => 50,
    ]);

    expect($result['afterFertilizerAmount'])->toBe(900)
        ->and($result['machineAmount'])->toBe(300.0)
        ->and($result['afterMachineAmount'])->toBe(600.0)
        ->and($result['netProfit'])->toBe(550.0)
        ->and($result['landlordRevenue'])->toBe(275.0)
        ->and($result['farmerRevenue'])->toBe(275.0);
});

it('rounds machine amount when one-third of remaining revenue is not an integer', function () {
    $result = AbastractCalculationReport::calculateProfitDistribution(1001, [
        'fertilizerExpenseAmount' => 100,
        'implementAndSeedExpenseAmount' => 0,
    ]);

    expect($result['afterFertilizerAmount'])->toBe(901)
        ->and($result['machineAmount'])->toBe(300.0)
        ->and($result['afterMachineAmount'])->toBe(601.0);
});
