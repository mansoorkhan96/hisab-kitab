<?php

use App\Models\Calculation;
use App\Models\CottonPickingDaily;
use App\Models\CottonPickingRound;
use App\Models\FarmingResource;
use App\Models\Labourer;
use App\Models\Ledger;
use App\Models\LoanPayment;
use App\Models\User;
use App\ValueObjects\CottonCropCalculationReport;
use Illuminate\Support\Number;

it('calculates cotton revenue after labour and kamdari, then loan-adjusted farmer share', function () {
    $admin = actingAsAdmin();
    $team = $admin->team;
    $season = makeCurrentSeason($team, [
        'cotton_rate_per_kg' => 8_000,
        'cotton_labour_rate_per_kg' => 25,
    ]);
    $farmer = User::factory()->farmer()->create(['team_id' => $team->id]);

    $calculation = Calculation::factory()->cotton()->create([
        'team_id' => $team->id,
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'kamdari' => 1_000,
    ]);

    $round = CottonPickingRound::factory()->create([
        'team_id' => $team->id,
        'crop_season_id' => $season->id,
        'user_id' => $farmer->id,
    ]);

    $labourer = Labourer::factory()->create(['team_id' => $team->id]);

    CottonPickingDaily::factory()->create([
        'cotton_picking_round_id' => $round->id,
        'labourer_id' => $labourer->id,
        'kgs_picked' => 250,
        'picking_date' => '2026-09-01',
    ]);
    CottonPickingDaily::factory()->create([
        'cotton_picking_round_id' => $round->id,
        'labourer_id' => $labourer->id,
        'kgs_picked' => 150,
        'picking_date' => '2026-09-02',
    ]);

    $fertilizer = FarmingResource::factory()->fertilizer()->create(['team_id' => $team->id]);
    $pesticide = FarmingResource::factory()->pesticide()->create(['team_id' => $team->id]);
    $seed = FarmingResource::factory()->seed()->create(['team_id' => $team->id]);
    $implement = FarmingResource::factory()->implement()->create(['team_id' => $team->id]);

    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $fertilizer->id,
        'quantity' => 1,
        'rate' => 1_600,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $pesticide->id,
        'quantity' => 1,
        'rate' => 500,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $seed->id,
        'quantity' => 1,
        'rate' => 400,
    ]);
    Ledger::factory()->create([
        'user_id' => $farmer->id,
        'crop_season_id' => $season->id,
        'farming_resource_id' => $implement->id,
        'quantity' => 1,
        'rate' => 800,
    ]);

    LoanPayment::factory()->create([
        'user_id' => $farmer->id,
        'calculation_id' => $calculation->id,
        'amount' => 200,
    ]);

    $report = CottonCropCalculationReport::make($calculation->fresh(['cropSeason', 'user']));

    // 400 kg; labour = 400*25 = 10000; gross = (400/40)*8000 = 80000
    // revenue = 80000 − 10000 − 1000 kamdari = 69000
    // fert 2100 → after 66900; machine 22300; after machine 44600; implement+seed 1200 → net 43400
    // shares 21700; farmer revenue 21700 − loan 200 = 21500
    expect($report->totalCottonKgs)->toBe('10-0')
        ->and($report->grossRevenue)->toBe(80_000.0)
        ->and($report->labourCost)->toBe(10_000.0)
        ->and($report->amountAfterLabourCost)->toBe(70_000.0)
        ->and($report->revenue)->toBe(69_000.0)
        ->and($report->fertilizerExpenseAmount)->toBe(2_100.0)
        ->and($report->remainingAfterFertilizerExpenseAmount)->toBe(Number::currency(66_900, 'PKR'))
        ->and($report->kamdariAmount)->toBe(1_000.0)
        ->and($report->machineAmount)->toBe(22_300.0)
        ->and($report->remainingAfterMachineAmount)->toBe(Number::currency(44_600, 'PKR'))
        ->and($report->implementAndSeedExpenseAmount)->toBe(1_200.0)
        ->and($report->remainingAfterImplementAndSeedExpenseAmount)->toBe(Number::currency(43_400, 'PKR'))
        ->and($report->landlordRevenue)->toBe(21_700.0)
        ->and($report->farmerGrossRevenue)->toBe(21_700.0)
        ->and($report->loanPaymentsAmount)->toBe(200.0)
        ->and($report->farmerRevenue)->toBe(21_500.0);
});
